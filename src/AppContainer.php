<?php declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use PDO;
use Pimple\Container;
use Todo\TodoMapper;
use Todo\TodoTransformer;

class AppContainer extends Container
{
    /**
     * Constructor.
     *
     * @param array$args the array of parameters passed into the OpenWhisk action
     */
    public function __construct(array $args)
    {
        if (!isset($args['__bx_creds']['elephantsql']['uri'])) {
            throw new InvalidArgumentException("ElephantSQL instance has not been bound");
        }
        $credentials = parse_url($args['__bx_creds']['elephantsql']['uri']);

        $configuration ['settings'] = [
            'base_url' => $this->determineBaseUrl($args),
        ];

        /**
         * Factory to create a PDO instance
         */
        $configuration[PDO::class] = function (Container $c) use ($credentials) {
            $host = $credentials['host'];
            $port = $credentials['port'];
            $dbName = trim($credentials['path'], '/');
            $user = $credentials['user'];
            $password = $credentials['pass'];

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbName;user=$user;password=$password";

            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        };

        /**
         * Factory to create a TodoMapper instance
         */
        $configuration[TodoMapper::class] = function (Container $c) : TodoMapper {
            return new TodoMapper($c[PDO::class]);
        };

        /**
         * Factory to create a TodoTransformer instance
         */
        $configuration[TodoTransformer::class] = function (Container $c) : TodoTransformer {
            return new TodoTransformer($c['settings']['base_url']);
        };

        /**
         * Factory to create a Manager instance
         */
        $configuration[Manager::class] = function (Container $c) : Manager {
            $baseUrl = $c['settings']['base_url'];

            $manager = new Manager();
            $manager->setSerializer(new ArraySerializer($baseUrl));
            return $manager;
        };

        parent::__construct($configuration);
    }

    /**
     * Determine the base URL from the x-forwarded-url header by
     * removing __ow_path from the end
     */
    private function determineBaseUrl(array $args) : string
    {
        if (!isset($args['__ow_headers']['x-forwarded-url'])
            || !isset($args['__ow_path'])) {
            return '';
        }

        $url = $args['__ow_headers']['x-forwarded-url'];

        $path = $args['__ow_path'];
        $length = strlen($url) - strlen($path);
        $baseUrl = substr($url, 0, $length);

        // Work around API Gateway's __ow_path inconsistency
        // weirdly, when the API Gateway definition has a placeholder in it (such as {id}), then
        // __ow_path will include the "/ow-php-todo-backend/todos" prefix, but if the definition does not
        // include a placeholder, then __ow_path doesn't include this prefix.
        $baseUrl = str_replace('/ow-php-todo-backend/todos', '', $baseUrl);
        $baseUrl .= '/ow-php-todo-backend/todos';
        return $baseUrl;
    }
}
