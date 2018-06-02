# Todo-Backend for OpenWhisk PHP

This is an [OpenWhisk](http://openwhisk.org) API written in PHP that implements [Todo-Backend](http://todobackend.com).


It uses [Pimple](https://pimple.symfony.com) for dependency injection and [Fractal](http://fractal.thephpleague.com) for the presentation layer. The data is stored in (PostgreSQL](https://www.postgresql.org) hosted by [ElephantSQL](https://www.elephantsql.com) via PDO.

# Set up


1. Ensure you have an an [IBM Cloud](https://www.ibm.com/cloud/) account to deploy to their OpenWhisk service (called  Functions)
2. Set up the [`bx` command line tool](https://console.bluemix.net/openwhisk/learn/cli) and set your [workspace](https://console.bluemix.net/docs/cli/reference/bluemix_cli/bx_cli.html#bluemix_target).
3. Set up a (free) ElephantSQL service in your IBM Cloud and bind the credentials to an OpenWhisk package called "todo-backend":

    ```shell
    $ bx service create elephantsql turtle bookshelf-db
    $ bx service key-create bookshelf-db key1
    $ bx wsk package create todo-backend
    $ bx wsk service bind elephantsql --instance bookshelf-db todo-backend
    ```
4. Install the [Serverless Framework](https://serverless.com)

    ```shell
    $ npm install --global serverless serverless-openwhisk
    ```

5. Clone this repo
6. Run the package managers:

    ```shell
    $ npm install
    $ composer install
    ```

7. Deploy the API using the `sls` command:

    ```shell
    $ sls deploy
    ```

    Take a note of the URL to the "`list-todos`" endpoint in the "**`endpoints (api-gw)`**" section as we'll need it later.

8. Create the database table

    ```shell
    $ sls invoke -f create-schema
    ```

Your API is now up and running.

## Running the test

Visit the [TodoBacked test suite](http://todobackend.com/specs/index.html) and enter the `list-todos` endpoint from the `endpoints (api-gw)` section when you ran `sls deploy`. You can run `sls info`  to view it again. 

Press the *Run tests* button.

![Screen shot of successful test run](https://raw.githubusercontent.com/akrabat/ow-php-todo-backend/master/tests-screenshot.png)
