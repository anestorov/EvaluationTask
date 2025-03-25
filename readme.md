# Evaluation Project

## Installation

Make sure you have PHP and Composer globally installed.  
To verify you can run `sh php -v` and `composer -h` in your terminal.

To install and set up the project, follow these steps:

1. Clone the repository:
    ```sh
    git clone https://github.com/anestorov/EvaluationTask.git
    ```
2. Navigate to the project directory:
    ```sh
    cd [EvaluationTask]
    ```
3. Install the required dependencies:

    ```sh
    composer install
    ```

\*Notice that data and vendors subdirectories should have write access

## 1: Advertising Bid Auction

According to me the main goal in this task, in order to achieve performance, was to use file cursor and traverse the csv file using **_fgetcsv()_** command. In this was you do not need to store file's content to memory and you can parse sigle row at a time.

In my solution I created a generic **CSV parser** class **_(lib/CsvParser.php)_** that accept any data Model class that extends Model Interface. Its parse function accepts filePath and a callback function that is envoked for each parser row of the file. Parameter to the callback function is an instance of the data Model class passed to the constructor.

In this task I tried to follow **_SOLID principles_** and strict **_PHP types_** and **_PHPDoc_** specifications for creating generic types and code autocompletion.

### Run the code in CLI

Open your terminal, `cd` to the projects root directory and execute:

```sh
    php task1/run.php [filename]
```
\*Replace __[filename]__ with the path to the input file containing the auction data. Sample data file are 
located in **data/task1/** folder

Existing test files can be processed using the following commands
```
    php task1/run.php case1.csv
    php task1/run.php case2.csv
    php task1/run.php case3.csv
```



## 2: Word Frequency Counter

### Run the code UI in Browser

Open your terminal, `cd` to the projects root directory and execute the following to start your backend server.

```
composer run task2
```

Then Open browser to address http://localhost:8888 or http://127.0.0.1:8888

## 3: Escape a labyrinth

### Run the code UI in Browser

Open your terminal, `cd` to the projects root directory and execute the following to start your backend server.

```
composer run task2
```

Then Open browser to address http://localhost:8888 or http://127.0.0.1:8888

### Run the code in CLI

```
php task3/run.php [MapFile]
```

## Testing
This project tests are located in __tests__ directory arranged by tasks.  

#### You can run all tests using
```
composer test
```
or
```
php vendor/bin/phpUnit
```
