<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="./tests/Bootstrap.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnError="false"
         stopOnFailure="false"
         syntaxCheck="true"
         verbose="true">
>
    <testsuites>
        <testsuite name="Mockery Test Suite">
            <directory suffix="Test.php">./tests</directory>
            <exclude>./tests/Mockery/MockingVariadicArgumentsTest.php</exclude>
            <file phpVersion="5.6.0" phpVersionOperator=">=">./tests/Mockery/MockingVariadicArgumentsTest.php</file>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./library/</directory>
            <exclude>
                <file>./library/Mockery/Adapter/Phpunit/MockeryPHPUnitIntegration.php</file>
            </exclude>
        </whitelist>
    </filter>
    <listeners>
        <listener
            class='Mockery\Adapter\Phpunit\TestListener'
            file='./library/Mockery/Adapter/Phpunit/TestListener.php'/>
    </listeners>
</phpunit>
