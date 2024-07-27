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
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    public function bootstrap( Application $app ) {
        if ( $app->configurationIsCached() ) {
            return;
        }

        $this->checkForSpecificEnvironmentFile( $app );

        // Check if the .env file exists before proceeding further.
        // In Hybrid Core, the presence of a .env file is optional. The application
        // can function without it, depending on the specific configuration and setup.
        // If the .env file does not exist, simply return to avoid unnecessary processing.
        if ( ! file_exists( $app->environmentFilePath() ) ) {
            return;
        }

        try {
            $this->createDotenv( $app )->safeLoad();
        } catch ( \Dotenv\Exception\InvalidFileException $e ) {
            $this->writeErrorAndDie( $e );
        }
    }

    /**
     * Detect if a custom environment file matching the HYBRID_CORE_ENV exists.
     *
     * @param  \Hybrid\Contracts\Core\Application $app
     * @return void
     */
    protected function checkForSpecificEnvironmentFile( $app ) {
        if ( $app->runningInConsole() &&
            ( $input = new ArgvInput() )->hasParameterOption( '--env' ) &&
            $this->setEnvironmentFilePath( $app, $app->environmentFile() . '.' . $input->getParameterOption( '--env' ) )
        ) {
            return;
        }

        $environment = Env::get( 'HYBRID_CORE_ENV' );

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
     * @param  \Dotenv\Exception\InvalidFileException $e
     * @return never
     */
    protected function writeErrorAndDie( \Dotenv\Exception\InvalidFileException $e ) {
        $output = ( new ConsoleOutput() )->getErrorOutput();

        $output->writeln( 'The environment file is invalid!' );
        $output->writeln( $e->getMessage() );

        exit( 1 );
    }

}
