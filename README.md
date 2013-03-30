# Complete documentation coming soon...# CodeIgniter Base Model

A simple base model that provides CRUD and common functions for faster development

* [Installation](https://github.com/ngiannini/CodeIgniter-Base-Model#installation)
* [CRUD](https://github.com/ngiannini/CodeIgniter-Base-Model#crud)
* [Utility Functions](https://github.com/ngiannini/CodeIgniter-Base-Model#utility-functions)
* [Return Types]https://github.com/ngiannini/CodeIgniter-Base-Model#return-types
* [Soft Delete]https://github.com/ngiannini/CodeIgniter-Base-Model#soft-delete

## Installation

To install the base model save `MY_Model.php` to the `application/core` directory

Use the base model by extending `MY_Model` rather than `CI_Model` like so:

    <?php if(!defined('BASEPATH')) exit('No direct script access allowed.');

    class User_M extends MY_Model {
        protected $_table = 'users';
        protected $_timezone = 'America/New_York';
        protected $_timestamps = true;
        protected $_soft_delete = true;
        protected $_return_array = true;

        public function construct()
        {
            parent::__construct();
        }
    }

## CRUD

The basic CRUD (create, read, update, delete) functions follow a simple naming convention when dealing with different amount of data.

Here are some examples of basic usage:

    <?php
        // Insert a new member
        $data = array(
            'username' => 'Nick',
            'password' => '1234'
        );
        $this->user_m->create($data);

        // Insert multiple users
        $data = array(
            '1' = array(
                'username' => 'John',
                'password' => 'abcd'
            ),
            '2' = array(
                'username' => 'Jane',
                'password' => '1a2b' 
            )
        );
        $this->user_m->create_many($data);

        // Get user with id of 2
        $this->user_m->get(2);

        // Get the first 3 users who have a status of Active
        $this->user_m->get_many(array('status' => 'Active'), 3);

        // Get all the users
        $this->user_m->get_all();

This pattern continues with updating, deleting, and restoring data as well.

    <?php
        $this->user_m->update($key, $data);
        $this->user_m->update_many($filters, $data);

        $this->user_m->delete($key, $soft);
        $this->user_m->delete_many($filters, $soft);
        $this->user_m->delete_all($soft);

        $this->user_m->restore($key);
        $this->user_m->restore_many($filters);

## Utility Functions

The base model also comes with a few utility functions that simplify some common tasks.

    <?php
        // Count all the records
        $this->user_m->count();

        // Count all of our active users
        $this->user_m->count_many(array('status') => 'Active');

        // Get the name of our table 'users'
        $this->user_m->table();

        // Find out what fields our table has
        $this->user_m->fields();

## Return Types

By default, the base model will return objects when using "get" functions by utilizing CodeIgniter's `row()` and `result()` functions.  To data as pure arrays you can set `$_return_array = true`, which will cause the model to use `row_array()` and `result_array()` instead.

## Soft Delete

By default, the base model will permenantly delete data, which is not always the desired case.  By enabling a 'soft delete' globally by setting `protected $_soft_delete = true` or on a case by case basis by setting the `$soft` parameter to `true` in the delete functions the model will instead add a timestamp to the specified deleted field.