#!/bin/bash

set -e
# set -x

#######################################################################################################################
# This script runs semi end-to-end tests for Mockery.
#
# It clones the repositories listed in the `projects` array,
# installs Mockery from the local filesystem,
# and runs PHPUnit for each PHP version listed in the `php_versions` array.
#
# This is useful to test Mockery against different versions of other projects and frameworks.
#
#######################################################################################################################

php_versions=(
    "8.2"
    "8.3"
)

projects=(
    "laravel/framework"
    "Brain-WP/BrainMonkey"
    "filp/whoops"
)

mockery_path="$(pwd)"

resources_path="$mockery_path/../mockery-resources"

mockery_branch=$(git -C "$mockery_path" rev-parse --abbrev-ref HEAD)
mockery_sha=$(git -C "$mockery_path" rev-parse HEAD | cut -c1-8)
mockery_version="dev-$mockery_branch#$mockery_sha"

echo "===> Running e2e tests"
echo "PHP versions: [ ${php_versions[*]} ]"
echo "Test Projects: [ ${projects[*]} ]"
echo " "
echo "Mockery branch: $mockery_branch"
echo "Mockery SHA: $mockery_sha"
echo "Mockery version: $mockery_version"
echo "Mockery path: $mockery_path"
echo "Resource path: $resources_path"
echo " "


mkdir -p "$resources_path" || { echo "Failed to create directory $resources_path"; exit 1; }
cd "$resources_path" || { echo "Failed to change directory to $resources_path"; exit 1; }

for project in "${projects[@]}"
do
    project_path="$resources_path/$project"

    if [ ! -d "$project_path" ]; then
        echo "Cloning $project to $project_path"

        git clone "git@github.com:$project.git" "$project_path" --depth=1 || { echo "Failed to clone $project"; exit 1; }
    else
        echo "Pulling $project"

        git -C "$project_path" fetch --depth=1 || { echo "Failed to fetch $project"; exit 1; }

        git -C "$project_path" pull --rebase --autostash || { echo "Failed to pull $project"; exit 1; }
    fi

    cd "$project_path" || { echo "Failed to change directory to $project_path"; exit 1; }

    echo "Installing Mockery version $mockery_version"

    for php_version in "${php_versions[@]}"
    do
        echo "Running PHPUnit for PHP version $php_version"

        docker run -it --rm -v "$mockery_path":/opt/mockery -v "$project_path":/opt/workspace -w /opt/workspace ghcr.io/ghostwriter/php:"$php_version"-pcov sh -c "composer config repositories.local '{\"type\": \"path\", \"url\": \"/opt/mockery\"}' && composer require 'mockery/mockery:$mockery_version' --with-dependencies --ignore-platform-reqs --no-scripts --no-plugins --dev --no-interaction && php vendor/bin/phpunit" || { echo "Failed to run PHPUnit for $project PHP version $php_version"; exit 1; }
    done
done

rm -rf "$resources_path" || { echo "Failed to remove directory $resources_path"; exit 1; }
