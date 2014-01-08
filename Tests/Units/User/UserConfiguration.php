<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\User;
use mageekguy\atoum\test;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Parser;
use M6Web\Bundle\DomainUserBundle\User\UserConfiguration as TestedClass;

/**
 * Class UserConfiguration
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserConfiguration extends test
{
    public function testProcessedConfig()
    {
        $yamlParser = new Parser();
        $userConfig = $yamlParser->parse(file_get_contents(__DIR__.'/../../Fixtures/users/user2.yml'));
        $processor = new Processor();

        $this->assert
            ->array($processor->processConfiguration(new TestedClass(), [$userConfig]))
                ->isEqualTo(array(
                    'cache' => array(
                        'default' => 300,
                        'routes' => array(),
                    ),
                    'firewall' => array(
                        'user_access' => array(
                            'default_state' => true,
                            'lists' => array(),
                        ),
                        'allow' => array(
                            'default' => false,
                            'methods' => array(),
                            'resources' => array(),
                            'routes' => array(),
                        )
                    ),
                    'entities' => array(
                        'active' => true,
                        'myflag' => 3
                    ),
                ));
    }
}
 