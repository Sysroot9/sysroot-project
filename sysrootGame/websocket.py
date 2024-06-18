import asyncio
import websockets
import json
from aiohttp import web

connected = set()
connectedId = set()
playerIds = {}  # Novo dicionário para mapear WebSockets para playerIds
game_started = False
maxPlayers = 2


async def websocket_handler(request):
    ws = web.WebSocketResponse()
    await ws.prepare(request)
    connected.add(ws)
    global game_started
    print("Novo cliente WebSocket conectado.")

    try:

        while True:

            # Verifica se o jogador está atualmente registrado no servidor
            if ws.open:

                # Ping-Pong, verifica se o cliente está respondendo ao servidor.
                try:
                    pong_waiter = await ws.ping()
                    await asyncio.wait_for(pong_waiter, timeout=10)

                # O cliente demorou mais de 10 segundos para enviar um sinal ao servidor, portanto, foi desconectado.
                except asyncio.TimeoutError:
                    print(f"O cliente {ws} demorou para responder.")
                    break

            # O jogador atualmente não está registrado nos servidores, portanto, saiu.
            else:
                print(f"O cliente {ws} se desconectou.")
                break

            # Recebe todos os dados um a um do cliente.
            async for message in ws:
                data = json.loads(message)
                print(f"Recebido: {data}")

                # Verifica o tipo de solicitação recebida

                if data['type'] == 'connection' and data['is_online']:  # Solicitação de conexão

                    # Verifica se o jogador já não está em outra sessão.
                    if data['playerId'] not in connectedId:
                        # O jogador entrou no jogo.
                        response = f"Jogador {data['playerId']} entrou no jogo {data['gameId']}"
                        print(response)
                        connectedId.add(data['playerId'])
                        playerIds[ws] = data['playerId']  # Adicione o playerId ao dicionário

                        # O jogo se iniciou para todos.
                        if len(connected) == maxPlayers and not game_started:
                            game_start = "game.start"
                            await asyncio.wait([asyncio.create_task(ws.send(game_start)) for ws in connected])
                            game_started = True

                    # Conexão recusada, o usuário já está usando outra sessão.
                    else:
                        response = (f"A conexão de {ws} foi recusada. O ID fornecido já está sendo utilizado "
                                    f"por outro socket.")
                        print(response)
                        message = {
                            "type": "disconnected",
                            "err": "player.id.use"
                        }
                        await ws.send(json.dumps(message))
                        break

                elif data['type'] == 'game':  # Solicitação do jogo

                    if data['subtype'] == 'gameplay':
                        print("teste gameplay")

                    elif data['subtype'] == 'manage':
                        print("teste manage")

    # Como o jogador foj desconectado do servidor, aqui são excluídos os registros dele.
    finally:
        connected.remove(ws)
        if ws in playerIds and playerIds[ws] in connectedId:
            connectedId.remove(playerIds[ws])


async def handle_http_request(request):
    return web.FileResponse('index.html')


app = web.Application()
app.add_routes([
    web.get('/', handle_http_request),
    web.get('/ws', websocket_handler)
])

if __name__ == '__main__':
    web.run_app(app, port=80)  # Porta 80 para HTTP e WebSocket