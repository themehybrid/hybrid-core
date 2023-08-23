<?php

namespace Hybrid\Core\Bootstrap;

use Dotenv\Dotenv;
use Hybrid\Contracts\Core\Application;
use Hybrid\Tools\Env;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class LoadEnvironmentVariables {

    /**
     * Bootstrap the given application.
     *
     * @return void
     */
    public function bootstrap( Application $app ) {
        if ( $app->configurationIsCached() ) {
            return;
        }

        $this->checkForSpecificEnvironmentFile( $app );

        try {
            $this->createDotenv( $app )->safeLoad();
        } catch ( \Dotenv\Exception\InvalidFileException $e ) {
            $this->writeErrorAndDie( $e );
        }
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    protected function checkForSpecificEnvironmentFile( $app ) {
        if ( $app->runningInConsole() &&
            ( $input = new ArgvInput() )->hasParameterOption( '--env' ) &&
            $this->setEnvironmentFilePath( $app, $app->environmentFile() . '.' . $input->getParameterOption( '--env' ) ) ) {
            return;
        }

        $environment = Env::get( 'APP_ENV' );

        if ( ! $environment ) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app, $app->environmentFile() . '.' . $environment
        );
    }

    /**
     * Load a custom environment file.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @param  string                             $file
     * @return bool
     */
    protected function setEnvironmentFilePath( $app, $file ) {
        if ( is_file( $app->environmentPath() . '/' . $file ) ) {
            $app->loadEnvironmentFrom( $file );

            return true;
        }

        return false;
    }

    /**
     * Create a Dotenv instance.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return \Dotenv\Dotenv
     */
    protected function createDotenv( $app ) {
        return Dotenv::create(
            Env::getRepository(),
            $app->environmentPath(),
            $app->environmentFile()
        );
    }

    /**
     * Write the error information to the screen and exit.
     *
     * @return void
     */
    protected function writeErrorAndDie( \Dotenv\Exception\InvalidFileException $e ) {
        $output = ( new ConsoleOutput() )->getErrorOutput();

        $output->writeln( 'The environment file is invalid!' );
        $output->writeln( $e->getMessage() );

        exit( 1 );
    }

}
