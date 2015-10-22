Reddit Demo
===============

This project was created to show some code.

This project as divided in two mini-projects, back-end and front-end using JSON for communications between them.

You can see the demo in https://reddit.chuque.org/

For back-end:

* PHP
* Phalcon (Really PHP Framework, its performance for huge systems it is really awesome)
* Mysql database, yes I know, from my point of view this is not the best option, anyway, this is only a demo.
* Apache as HTTP server
* Linux of corse to deploy everything.

Front-end
* AngularJs
* Require.Js
* Coutch potato for lazy loads
* Bootstrap
* I added Jquery but just because of Bootstrap.

Everything is modularized as you can see in the code.

Also there is some things I consider could be improved.

* Add memcache layer, I did not have time to added it.
* Create a HTTP global service in Front-End
* Improve the error management (Front-end)
* Obviously, styling (I am not a good designer)
* Compile the project (FE) to minimize the size and just let 1 compiled file (bundle.js for example)

Here is the original request:

> "Build a working website where users can create accounts and submit links to other sites. Other users can upvote or downvote those links exactly one time giving or taking away one point. The links are shown on the page with their score to any user. Each 5 minutes, every score 'decays' by 1 point." (Build us a lightweight reddit-like application)" ...


