#### How to create database and start server:

Run following commands from root of the project:

`php bin/console doctrine:database:create`

`php bin/console doctrine:schema:create`

`symfony server:start`

#### How to play:

Send <code>GET</code> request to <code>{host}/start</code> to start new game with new <code>id</code>.

Send <code>POST</code> request to <code>{host}/move/{id}</code> (<code>id</code> from created game) with 2 (3) params: <code>start</code>, <code>end</code> and optional <code>transformTo</code> to move piece from <code>start</code> to <code>end</code> (and piece id to transform for pawn that reached last line). Example: <code>$post_data = array("start" => "a8", "end" => "a2", "target" => 7)</code>