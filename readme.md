# Redis OAuth2 Client Generator

Redis Client Generator to be used with ThePhpLeague's OAuth2 Package

## Quick Reference

 - [Installation](#installation)
 - [Commands](#commands)

## Installation

TODO

## Commands

#### Create Client

In order to create a new client, use the command:
`oauth clients:create {name} {password} {redirect_uri = ''} {grantType = 'client_credentials'}`,

where:

`name` => The name of the application, [**REQUIRED**],
`password` => A password to use when you need to delete a client or re-generate his secret [**REQUIRED**],
`redirect_uri` => The redirect URI you want to make use of [**OPTIONAL**],
`grantType` => The grant type you want to use [**OPTIONAL**]

#### Delete Client

In order to delete a specific client, use the command:
`oauth clients:delete {client_id} {password}`,

where:

`client_id` => The client's id, [**REQUIRED**],
`password` => The password the user used when created the client [**REQUIRED**]

#### List Clients

In order to list all available clients, use the command `oauth clients:list`.

#### Fetch Client (or client's secret)

In order to fetch a specific client or just fetch a client's secret, use the command:
`oauth clients:fetch {client_id}`,

where:

`client_id` => The client's id, [**REQUIRED**],

and in case you want to fetch a specific user's secret, you can use the `--show-secret` flag in the command:
eg. `oauth clients:fetch {client_id} --show-secret {password}`

where:

`password` => The password the user used when created the client [**REQUIRED**]
