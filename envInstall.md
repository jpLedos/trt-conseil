# projet ECF BACK TRT CONSEIL 

composer require symfony/google-mailer --ignore-platform-reqs
composer require twig/inky-extra --ignore-platform-reqs
composer require twig/cssinliner-extra

symfony.exe server:ca:install 


#.env
MAILER_DSN=gmail://youremail@gmail.com:yourpassword@default?verify_peer=0