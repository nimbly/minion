<?php
/**
 * Created by PhpStorm.
 * User: brent
 * Date: 2/22/17
 * Time: 1:04 PM
 */

namespace minion\Config;


use Symfony\Component\Console\Style\SymfonyStyle;

class Config
{
    /**
     * @param $file
     * @param $data
     */
    public static function make($file, $data)
    {
        file_put_contents($file, $data);
    }

    /**
     * @param SymfonyStyle $style
     * @return array
     */
    public static function interactive(SymfonyStyle $style)
    {
        $env = [];

        $style->section('Remote');
        $env['remote']['path'] = $style->ask('What is the remote path on the servers where code should be deployed?', '/var/www');
        $env['remote']['releaseDir'] = $style->ask('What directory should be used to hold the releases?', 'releases');
        $env['remote']['symlink'] = $style->ask('What symlink name should be used to link to the current release?', 'current');
        $env['remote']['keepReleases'] = $style->ask('How many releases should be kept before pruning?', 5, function ($number) {
            if (!is_integer($number)) {
                throw new \RuntimeException('You must type an integer.');
            }

            return $number;
        });

        $style->section('Code');
        $env['code']['scm'] = $style->choice('What source code management tool do you use?', ['git', 'svn'], 'git');
        $env['code']['repo'] = $style->ask('What is the repository URL?');
        $env['code']['branch'] = $style->ask('What branch should be deployed?', 'master');

        $style->section('Authentication');
        $authMethod = $style->choice('What is the SSH authentication method?', ['key', 'password'], 'key');
        $env['authentication']['username'] = $style->ask('User name');

        if( $authMethod == 'password' ){
            $env['authentication']['password'] = $style->askHidden('Password');
        } else {
            $env['authentication']['password'] = null;
        }

        if( $authMethod == 'key' ){
            $env['authentication']['key'] = $style->ask('Key file');

            if( $style->confirm('Does the key require a passphrase?') ){
                $env['authentication']['passphrase'] = $style->askHidden('Passphrase');
            } else {
                $env['authentication']['passphrase'] = null;
            }
        }
        else {
            $env['authentication']['key'] = null;
            $env['authentication']['passphrase'] = null;
        }

        $style->section('Environments');

        do {

            $environmentName = $style->ask('Environment name (eg. staging, production, etc)');

            $env['environments'][$environmentName]['preDeploy'] = null;
            $env['environments'][$environmentName]['postDeploy'] = null;
            $env['environments'][$environmentName]['strategy'] = $style->ask('Strategy', 'release, symlink, prune');
            $env['environments'][$environmentName]['servers'] = [];

            $style->section("Servers for {$environmentName}");

            do {
                $env['environments'][$environmentName]['servers'][] = [
                    'host' => $style->ask('Server host')
                ];

                $anotherServer = $style->confirm('Add another server?');
            } while( $anotherServer );


            $anotherEnvironment = $style->confirm('Add another environment?');
        } while( $anotherEnvironment );

        return $env;
    }
}