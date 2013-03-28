# CodeIgniter Base Model

A simple base model that provides CRUD and common functions for faster development

## Installation

To install the base model save `MY_Model.php` to the `application/core` directory

### Usage

Use the base model by extending `MY_Model` rather than `CI_Model` like so:

    <?php if(!defined('BASEPATH')) exit('No direct script access allowed.');
    class User_M extends MY_Model {

    }