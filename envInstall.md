# projet ECF BACK TRT CONSEIL 

composer require symfony/google-mailer --ignore-platform-reqs
composer require twig/inky-extra --ignore-platform-reqs
composer require twig/cssinliner-extra
composer install --ignore-platform-reqs
La base de donnée :


symfony.exe server:ca:install 
composer install --no-dev --optimize-autoloader --prefer-dist  --ignore-platform-reqs

#.env
MAILER_DSN=gmail://youremail@gmail.com:yourpassword@default?verify_peer=0
APP_ENV=prod 