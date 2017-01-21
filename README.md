![the.church](https://thechur.ch/bundles/churchteaser/images/logo2.png)

[![Build Status](https://travis-ci.org/church/thechurch.svg?branch=develop)](https://travis-ci.org/church/thechurch) [![Coverage Status](https://coveralls.io/repos/github/church/thechurch/badge.svg?branch=develop)](https://coveralls.io/github/church/thechurch?branch=develop)

## Mission
By the sacrifice and love of Jesus Christ our Lord, according to His eternal
purpose, [thechur.ch](https://thechur.ch) exists to promote the unity of His
church locally and globally, the literal priesthood of all believers, and the
individual and corporate spiritual growth of the church.

> “Now I urge you, brothers, in the name of our Lord Jesus Christ, that all of
  you agree in what you say, that there be **no divisions among you**, and that
  you **be united** with the same understanding and the same conviction.”
  (1 Corinthians 1:10)

## Justification
The only biblical church divisions found in scripture are geographical. The
majority of the epistles are addressed to the entire church of that city:

> “To all who are in **Rome**, loved by God, called as saints. Grace to you and
  peace from God our Father and the Lord Jesus Christ.” (Romans 1:7)

> “To God’s church at **Corinth**, to those who are sanctified in Christ Jesus
  and called as saints, with all those in every place who call on the name of
  Jesus Christ our Lord—both their Lord and ours.” (1 Corinthians 1:2)

> “Paul, an apostle of Christ Jesus by God’s will, and Timothy our brother: To
  God’s church at **Corinth**, with all the saints who are throughout Achaia.”
  (2 Corinthians 1:1)

> “Paul, an apostle—not from men or by man, but by Jesus Christ and God the
  Father who raised Him from the dead—and all the brothers who are with me: To
  the churches of **Galatia**.” (Galatians 1:1-2)

> “Paul, an apostle of Christ Jesus by God’s will: To the faithful saints in
  Christ Jesus at **Ephesus**.” (Ephesians 1:1)

> “Paul and Timothy, slaves of Christ Jesus: To all the saints in Christ Jesus
  who are in **Philippi**, including the overseers and deacons.” (Philippians 1:1)

> “To the saints in Christ at **Colossae**, who are faithful brothers. Grace to
  you and peace from God our Father.” (Colossians 1:2)

> “Paul, Silvanus, and Timothy: To the church of the **Thessalonians** in God
  the Father and the Lord Jesus Christ. Grace to you and peace.”
  (1 Thessalonians 1:1)

> “Paul, Silvanus, and Timothy: To the church of the **Thessalonians** in God
  our Father and the Lord Jesus Christ.” (2 Thessalonians 1:1)

> “John: To the seven churches in Asia. Grace and peace to you from the One who
  is, who was, and who is coming; from the seven spirits before His throne; and
  from Jesus Christ, the faithful witness, the firstborn from the dead and the
  ruler of the kings of the earth.” (Revelation 1:4-5)

> “I was in the Spirit on the Lord’s day, and I heard a loud voice behind me
  like a trumpet saying, 'Write on a scroll what you see and send it to the
  seven churches: **Ephesus**, **Smyrna**, **Pergamum**, **Thyatira**,
  **Sardis**, **Philadelphia**, and **Laodicea**.'” (Revelation 10:11)

## Project
[thechur.ch](https://thechur.ch) exists as a tool to help unify the global and
local church. The goal is to build an online tool that can facilitate
communication for the one church of each city, so that the church may be united.
The goal is not to replace existing social networks (like
[Facebook](https://www.facebook.com) and [Twitter](https://twitter.com)), but
instead augment the experience. [thechur.ch](https://thechur.ch) is not made up
of friends and followers, but rather people in cities; as well as the people who
live closest to you.

![thechur.ch City Feed Mockup](https://docs.google.com/uc?id=0By6fCOSDOhkvT3RDWGdjdUpUZjg)

## Contributing

### Requirements
After forking and cloning the repository, you will need the following items:

1. [Yahoo! BOSS Geo](https://developer.yahoo.com/boss/geo/) API Credentials.
2. [Yahoo! GeoPlanet](https://developer.yahoo.com/geo/geoplanet/) API Credientials.
3. [Mandrill](http://mandrill.com/) API Credentials.
4. Ensure that your system meets the
   [Symfony System Requirements](http://symfony.com/doc/current/reference/requirements.html).
5. An empty MySQL/MariaDB database.
6. [Composer](https://getcomposer.org/) to install the php dependencies.
7. [Bundler](http://bundler.io/) to install the ruby dependencies.
8. [Bower](http://bower.io) to install the front-end dependencies.
9. Ensure that Apache is pointed to the`web` directory.

### Installation
Run the following command inside the repository:
```
composer install
```

This will install all the php dependencies. Once the the dependencies are
installed, you will be asked a series of questions in order to generate an
`app/config/parameters.yml` file. You can adjust the configuration at any time
by changing the values in this file. See [app/config/parameters.yml.dist](https://github.com/church/thechurch/blob/develop/app/config/parameters.yml.dist)
for a sample configuration file.

Install the ruby dependencies by executing the following command in the root of
the repository:
```
bundle install
```

Install the front-end dependencies by executing the following command in the
root of the repository:
```
bower install
```

Create the database schema by executing the following command:
```
 php app/console doctrine:schema:create
```

Then load the `place type` data by executing the following command:
```
php app/console doctrine:fixtures:load
```

Lastly, you can dump the assets by executing:
```
php app/console assetic:dump

```

You may now access the site at [http://localhost/app_dev.php/](http://localhost/app_dev.php/)
