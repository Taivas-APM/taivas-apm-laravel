<h1 align="center">
  Taivas APM
</h1>

<p align="center">
  <strong>Taivas is an Application Performance Monitoring (APM) software for Laravel. :bar_chart:</strong>
</p>

Most apps are not continuously tested for best practices. Taivas solves that problem by allowing you realtime insight into the production performance
of your Laravel application. Taivas analyzes your requests and collects database queries, cache performance and much more. The free [hosted Taivas 
Frontend](https://app.taivas.io) allows you to see which requests take longer than they should,
which requests to optimize and provides many graphs about your application's performance history.

## :star: Highlights
 - Super easy to install (takes ~5 minutes)
 - Free [hosted frontend](https://app.taivas.io) so you don't have to setup it yourself
 - Extremely low performance impact. The redis persister adds only about 0.5ms to each request.


## :rocket: Installation
Require this package in the `composer.json` of your Laravel project.

```php
composer require taivas-apm/taivas-apm-laravel
```

Publish the configuration:

```bash
php artisan vendor:publish --provider="TaivasAPM\TaivasAPMServiceProvider"
```

Set the TAIVAS_SECRET key in your .env file. To do that, create a random string with tinker:

```bash
php artisan tinker
Str::random(32)
```

Execute the taivas migrations to create a table to store the request data

```bash
php artisan migrate
```

If your cors configuration is not open, allow access from our hosted web app in your cors config file (config/cors.php):

```
...
'paths' => ['your-api/*', 'taivas/*'],
...
'allowed_origins' => ['yourdomain.com', 'app.taivas.io'],
```

If you do not want to use the hosted frontend, you can [host it yourself](https://github.com/Taivas-APM/taivas-apm-app). However, you will have to make sure to keep it up to date.


#### :rocket: Open the [hosted web app](https://app.taivas.io)
Enter your domain and login with the user credentials from your own application. All communication happens between your browser and your own application.

## :thumbsup: Tips
 - For smaller sites (< 1 request/second) it's fine to use the sync driver.
 - For larger sites you should use the redis persister to move the load from your webserver to your cronjob server.
 - For larger sites you should set the lottery setting so only some of the requests are tracked.

## :sailboat: Roadmap
 * [ ] Specifying a non-default redis connection
 * [ ] Combine the `shouldTrack` logic from the Service Provider and the Tracker class
 * [ ] Automatic tests for all supported Laravel Versions
 * [ ] Custom analytics support
 * [ ] ClickHouse support
 * [ ] Managed request storage service

## Dependencies
 - Laravel >= 5.5
 - A Laravel supported database to store the requests
 - Redis, if you want to persist requests asynchronously
 

### Thanks
Thanks to Taylor Otwell & team for giving so much to the open source community. The code structure of this package is based on [Horizon](https://github.com/laravel/horizon).
