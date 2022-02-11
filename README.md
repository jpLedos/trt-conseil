# projet ECF BACK TRT CONSEIL 

This is a symfony project required for the formation Studi dev web fullstack.

## Getting Started to deploy it locally

### First , in you project folder :

```
git clone [github.com/jpLedos/trt-conseil](https://github.com/jpLedos/trt-conseil) yourProject
cd trt-conseil
composer install --ignore-platform-reqs
```

### Second step, configure your environnement :
```
APP_ENV=dev
APP_SECRET=xxxxxxxxx 
DATABASE_URL="mysql://root@127.0.0.1:3306/trt-conseil"
```


### modify MAILER_DSN  : 

###> symfony/google-mailer ###
## Gmail SHOULD NOT be used on production, use it in development only.
```
  MAILER_DSN=gmail://youemailaddress@gmail.com:yourpassword@default
```


### last  step , run the development server:

```bash
symfony server:start 
or
symfony server:start -d
```

Open [http://localhost:8000](http://localhost:8000) with your browser to see the result.


## Getting Started to deploy it in heroku

create a new app on heroku
add resource : JawsDB MySQL
Connect your github repository
Deploy branch master