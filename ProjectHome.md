# Model Generator 2.0 rc3 #
RC version ready in the trunk.

The latest updates are always in the trunk. It's a work in progress and you can
follow changes at source/changes section.

# How it works ? #
It just generates php classes. It can:

  1. connect to your database (using Zend\_Db\_Adapter\_Pdo\_MySql)
  1. get list of tables
  1. analyze each table (relations, dependencies, unique keys)
  1. generate model (file you will be working with), base model (your model will extend this file), table and base table.

# Requirements ! #

  1. Zend framework in your include path (1.10+)
  1. MySql
  1. PDO

Table MUST HAVE at least one primary key defined. It will puke an error otherwise. You can skip tables without primary keys.

# How to use it ? #

  1. Configure config.ini file
  1. Run: zg.bat (Win/cmd line) or in your shell "php zg.php" or /zg.php in your browser.

Your models will be now generated in the directory given in the configuration file.

# Features #

  1. generates phpDoc (columns, methods, return types etc., MySql type hints)
  1. recognizes prefixed tables when generating functions (ex: customer, customer\_address becomes $customer->findAddress() NOT $customer->findCustomer\_Address)
  1. generates models, models base, DbTables
  1. works from command line
  1. works out-of-the-box (at least should)

# Bugs? #

Yeah, probably a lot ! But I'm fixing those right away so do not hestitate to report those issues.

# LICENSE #

Google project creator didnt allow me to select this license, but this is the one that should be used with Generator's code:
http://sam.zoy.org/wtfpl/

# Support my addiction #
## buy me a beer if I helped or hire me. ##