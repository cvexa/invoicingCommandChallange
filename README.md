# invoicingCommandChallenge
Clipping's challenges you to create a PHP / JS application that lets you sum invoice documents in different currencies via a file.

# Local Development Server
Before using Laravel, make sure you have Composer installed on your machine.
After cloning the repo, execute : 
  ```sh
  composer install
  ```
followed by : 
  ```sh
  php artisan serve
  ```
  
# App Route
  ```sh
  GET /
  ```
eg. http://localhost:8000/

this route should load and show the form, 

Upload the CSV file (used the demo csv file structure, provided in the task)

Input a list of currencies and exchange rates (e.g. EUR:1,USD:0.987,GBP:0.878)

Define an output currency (for example: GBP)

Filter by a specific customer (as an optional input) => here I filter by VAT (this field is optional, if presented will show only for the requested customer VAT)

Note, that if we have a credit note, it should subtract from the total of the invoice and if we have a debit note, it should add to the sum of the invoice.

Submitting the form will return the sum of all documents, except the case if you send customer (vat) in the form.

There is FE and BE validations for the form, the FE valdiation is simple jQuery check if the fields are empty, the BE validation is more in depth validation : 

            'csvFile' => 'required|file|mimes:csv',
            'currencies' => 'required',
            'outputCurrency' => 'required',
            'customer' => 'sometimes',//vat number

after that I have additional validation, 

if the requested VAT (customer) doesn't exist trow an validation error

if requested output currency, its not in the provided currencies will trow an validation error

if the structure of the provided currencies doesnt match CUR:NUM , will trow an validation error

if the total sum is negative num < 0 after the credit sum, will cast it to 0 instead of showing negative result

------------------------------------------------------------------------------------------------------------------------------------------------------------------
