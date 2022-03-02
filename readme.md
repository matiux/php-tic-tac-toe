PHP Tic Tac Toe REST API
======



## Setup project
```shell
git clone https://github.com/matiux/php-tic-tac-toe.git && cd php-tic-tac-toe
cp docker/docker-compose.override.dist.yml docker/docker-compose.override.yml
sudo chmod -R 777 docker/data
./dc up -d
./dc composer install
./dc project setup-dev
```

## Play

In the examples below I use `jq` to format the output json of cURL calls.
You can ignore it or install it on your operating system.

### Start new game

```shell
curl --location --request POST 'localhost:8080/v1/new-game' | jq
```
#### Response

The play ID
```json
{
  "id": "b2ac2d46-8b9d-469a-abf5-aa839bae912b"
}
```

### Game status

```shell
curl --location --request GET 'localhost:8080/v1/b2ac2d46-8b9d-469a-abf5-aa839bae912b' | jq
```
#### Response

```json
{
  "board": [
    [
      null,
      null,
      null
    ],
    [
      null,
      null,
      null
    ],
    [
      null,
      null,
      null
    ]
  ],
  "winning": false,
  "play_finished": false,
  "winning_combination": []
}
```

### Move

```shell
curl --location --request POST 'localhost:8080/v1/move' \
--header 'Content-Type: application/json' \
--data-raw '{
    "play_id": "b2ac2d46-8b9d-469a-abf5-aa839bae912b",
    "player": "O",
    "position": 8
    }' | jq
```
#### Response

```json
{
  "board": [
    [
      null,
      null,
      null
    ],
    [
      null,
      null,
      null
    ],
    [
      null,
      null,
      "O"
    ]
  ],
  "winning": false,
  "play_finished": false,
  "winning_combination": []
}
```