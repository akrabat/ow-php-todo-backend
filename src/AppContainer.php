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
        $dbCredentialsUrl = $args['__bx_creds']['elephantsql']['uri'] ?? '';
        if ($dbCredentialsUrl === '') {
            throw new InvalidArgumentException('Missing database credentials');
        }

        $configuration ['settings'] = [
            'db_credentials_url' => $dbCredentialsUrl,
            'base_url' => $this->determineBaseUrl($args['__ow_headers'] ?? []),
        ];

        putenv("BASE_URL=".$configuration['settings']['base_url']);

        /**
         * Factory to create a PDO instance
         */
        $configuration[PDO::class] = function (Container $c) {
            $url = $c['settings']['db_credentials_url'];
            $credentials = parse_url($url);
            $db = trim($credentials['path'], '/');

            $dsn = "pgsql:host={$credentials['host']};port={$credentials['port']};"
                . "dbname=$db;user={$credentials['user']};password={$credentials['pass']}";

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
     * Determine the base URL from the x-forwarded-url header
     */
    private function determineBaseUrl(array $headers) : string
    {
        if (!isset($headers['x-forwarded-url'])) {
            return '';
        }

        $parts = explode('/', $headers['x-forwarded-url']);

        $last = array_pop($parts);
        if (is_numeric($last)) {
            // last element was an id, so next one is the resource
            array_pop($parts);
        }
        return implode('/', $parts);
    }
}
