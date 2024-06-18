import asyncio
import json
import websockets

connected = set()
connectedId = set()
playerIds = {}  # Novo dicionário para mapear WebSockets para playerIds
game_started = False
maxPlayers = 2


async def server(websocket, path):
    global game_started
    print(f"O cliente {websocket} se conectou ao servidor.")
    connected.add(websocket)  # Regista o jogador no servidor

    try:

        while True:

            # Verifica se o jogador está atualmente registrado no servidor
            if websocket.open:

                # Ping-Pong, verifica se o cliente está respondendo ao servidor.
                try:
                    pong_waiter = await websocket.ping()
                    await asyncio.wait_for(pong_waiter, timeout=10)

                # O cliente demorou mais de 10 segundos para enviar um sinal ao servidor, portanto, foi desconectado.
                except asyncio.TimeoutError:
                    print("Um jogador saiu do jogo")
                    break

            # O jogador atualmente não está registrado nos servidores, portanto, saiu.
            else:
                print("Um jogador saiu do jogo")
                break

            async for message in websocket:
                data = json.loads(message)
                print(f"Recebido: {data}")

                # Verifica o tipo de solicitação recebida
                if data['type'] == 'connection' and data['is_online']:

                    # Verifica se o jogador já não está em outra sessão.
                    if data['playerId'] not in connectedId:
                        # O jogador entrou no jogo.
                        response = f"Jogador {data['playerId']} entrou no jogo {data['gameId']}"
                        print(response)
                        connectedId.add(data['playerId'])
                        playerIds[websocket] = data['playerId']  # Adicione o playerId ao dicionário

                        # O jogo se iniciou para todos.
                        if len(connected) == maxPlayers and not game_started:
                            game_start = "O jogo começou"
                            await asyncio.wait([asyncio.create_task(ws.send(game_start)) for ws in connected])
                            game_started = True

                    # Conexão recusada, o usuário já está usando outra sessão.
                    else:
                        response = (f"A conexão de {websocket} foi recusada. O ID fornecido já está sendo utilizado "
                                    f"por outro socket.")
                        print(response)
                        message = {
                            "type": "disconnected",
                            "err": "already.id.use"
                        }
                        await websocket.send(json.dumps(message))
                        break

    finally:

        # Remove o jogador dos registros do servidor.
        connected.remove(websocket)
        if websocket in playerIds and playerIds[websocket] in connectedId:
            connectedId.remove(playerIds[websocket])


start_server = websockets.serve(server, "0.0.0.0", 8081)
print("Servidor rodando!")

asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()