# DirectusSDK::TablesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**add_column**](TablesApi.md#add_column) | **POST** /tables/{tableId}/columns | Create a column in a given table
[**add_row**](TablesApi.md#add_row) | **POST** /tables/{tableId}/rows | Add a new row
[**add_table**](TablesApi.md#add_table) | **POST** /tables | Add a new table
[**delete_column**](TablesApi.md#delete_column) | **DELETE** /tables/{tableId}/columns/{columnName} | Delete row
[**delete_row**](TablesApi.md#delete_row) | **DELETE** /tables/{tableId}/rows/{rowId} | Delete row
[**delete_table**](TablesApi.md#delete_table) | **DELETE** /tables/{tableId} | Delete Table
[**get_table**](TablesApi.md#get_table) | **GET** /tables/{tableId} | Returns specific table
[**get_table_column**](TablesApi.md#get_table_column) | **GET** /tables/{tableId}/columns/{columnName} | Returns specific table column
[**get_table_columns**](TablesApi.md#get_table_columns) | **GET** /tables/{tableId}/columns | Returns table columns
[**get_table_row**](TablesApi.md#get_table_row) | **GET** /tables/{tableId}/rows/{rowId} | Returns specific table row
[**get_table_rows**](TablesApi.md#get_table_rows) | **GET** /tables/{tableId}/rows | Returns table rows
[**get_tables**](TablesApi.md#get_tables) | **GET** /tables | Returns tables
[**update_column**](TablesApi.md#update_column) | **PUT** /tables/{tableId}/columns/{columnName} | Update column
[**update_row**](TablesApi.md#update_row) | **PUT** /tables/{tableId}/rows/{rowId} | Update row


# **add_column**
> add_column(table_id, opts)

Create a column in a given table

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

opts = { 
  table_name: "table_name_example", # String | Name of table to add
  column_name: "column_name_example", # String | The unique name of the column to create
  type: "type_example", # String | The datatype of the column, eg: INT
  ui: "ui_example", # String | The Directus Interface to use for this column
  hidden_input: true, # BOOLEAN | Whether the column will be hidden (globally) on the Edit Item page
  hidden_list: true, # BOOLEAN | Whether the column will be hidden (globally) on the Item Listing page
  required: true, # BOOLEAN | Whether the column is required. If required, the interface's validation function will be triggered
  sort: 56, # Integer | The sort order of the column used to override the column order in the schema
  comment: "comment_example", # String | A helpful note to users for this column
  relationship_type: "relationship_type_example", # String | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE
  related_table: "related_table_example", # String | The table name this column is related to (only used when storing relational data)
  junction_table: "junction_table_example", # String | The pivot/junction table that joins the column's table with the related table (only used when storing relational data)
  junction_key_left: "junction_key_left_example", # String | The column name in junction that is related to the column's table (only used when storing relational data)
  junction_key_right: "junction_key_right_example" # String | The column name in junction that is related to the related table (only used when storing relational data)
}

begin
  #Create a column in a given table
  api_instance.add_column(table_id, opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->add_column: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **table_name** | **String**| Name of table to add | [optional] 
 **column_name** | **String**| The unique name of the column to create | [optional] 
 **type** | **String**| The datatype of the column, eg: INT | [optional] 
 **ui** | **String**| The Directus Interface to use for this column | [optional] 
 **hidden_input** | **BOOLEAN**| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hidden_list** | **BOOLEAN**| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **BOOLEAN**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **String**| A helpful note to users for this column | [optional] 
 **relationship_type** | **String**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **related_table** | **String**| The table name this column is related to (only used when storing relational data) | [optional] 
 **junction_table** | **String**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junction_key_left** | **String**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junction_key_right** | **String**| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml



# **add_row**
> add_row(table_idcustom_data)

Add a new row

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

custom_data = "custom_data_example" # String | Data based on your specific schema eg: active=1&title=LoremIpsum


begin
  #Add a new row
  api_instance.add_row(table_idcustom_data)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->add_row: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **custom_data** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **add_table**
> add_table(opts)

Add a new table

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

opts = { 
  name: "name_example" # String | Name of table to add
}

begin
  #Add a new table
  api_instance.add_table(opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->add_table: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **String**| Name of table to add | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml



# **delete_column**
> delete_column(table_idcolumn_name)

Delete row

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

column_name = "column_name_example" # String | Name of column to return


begin
  #Delete row
  api_instance.delete_column(table_idcolumn_name)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->delete_column: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **column_name** | **String**| Name of column to return | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **delete_row**
> delete_row(table_idrow_id)

Delete row

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

row_id = 56 # Integer | ID of row to return from rows


begin
  #Delete row
  api_instance.delete_row(table_idrow_id)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->delete_row: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **row_id** | **Integer**| ID of row to return from rows | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **delete_table**
> delete_table(table_id)

Delete Table

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from


begin
  #Delete Table
  api_instance.delete_table(table_id)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->delete_table: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **get_table**
> GetTable get_table(table_id)

Returns specific table

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from


begin
  #Returns specific table
  result = api_instance.get_table(table_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_table: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 

### Return type

[**GetTable**](GetTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_table_column**
> GetTableColumn get_table_column(table_idcolumn_name)

Returns specific table column

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

column_name = "column_name_example" # String | Name of column to return


begin
  #Returns specific table column
  result = api_instance.get_table_column(table_idcolumn_name)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_table_column: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **column_name** | **String**| Name of column to return | 

### Return type

[**GetTableColumn**](GetTableColumn.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_table_columns**
> GetTableColumns get_table_columns(table_id)

Returns table columns

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from


begin
  #Returns table columns
  result = api_instance.get_table_columns(table_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_table_columns: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 

### Return type

[**GetTableColumns**](GetTableColumns.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_table_row**
> GetTableRow get_table_row(table_idrow_id)

Returns specific table row

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

row_id = 56 # Integer | ID of row to return from rows


begin
  #Returns specific table row
  result = api_instance.get_table_row(table_idrow_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_table_row: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **row_id** | **Integer**| ID of row to return from rows | 

### Return type

[**GetTableRow**](GetTableRow.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_table_rows**
> GetTableRows get_table_rows(table_id)

Returns table rows

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from


begin
  #Returns table rows
  result = api_instance.get_table_rows(table_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_table_rows: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 

### Return type

[**GetTableRows**](GetTableRows.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_tables**
> GetTables get_tables

Returns tables

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

begin
  #Returns tables
  result = api_instance.get_tables
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->get_tables: #{e}"
end
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetTables**](GetTables.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **update_column**
> update_column(table_idcolumn_name, opts)

Update column

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

column_name = "column_name_example" # String | Name of column to return

opts = { 
  data_type: "data_type_example", # String | The datatype of the column, eg: INT
  ui: "ui_example", # String | The Directus Interface to use for this column
  hidden_input: true, # BOOLEAN | Whether the column will be hidden (globally) on the Edit Item page
  hidden_list: true, # BOOLEAN | Whether the column will be hidden (globally) on the Item Listing page
  required: true, # BOOLEAN | Whether the column is required. If required, the interface's validation function will be triggered
  sort: 56, # Integer | The sort order of the column used to override the column order in the schema
  comment: "comment_example", # String | A helpful note to users for this column
  relationship_type: "relationship_type_example", # String | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE
  related_table: "related_table_example", # String | The table name this column is related to (only used when storing relational data)
  junction_table: "junction_table_example", # String | The pivot/junction table that joins the column's table with the related table (only used when storing relational data)
  junction_key_left: "junction_key_left_example", # String | The column name in junction that is related to the column's table (only used when storing relational data)
  junction_key_right: "junction_key_right_example" # String | The column name in junction that is related to the related table (only used when storing relational data)
}

begin
  #Update column
  api_instance.update_column(table_idcolumn_name, opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->update_column: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **column_name** | **String**| Name of column to return | 
 **data_type** | **String**| The datatype of the column, eg: INT | [optional] 
 **ui** | **String**| The Directus Interface to use for this column | [optional] 
 **hidden_input** | **BOOLEAN**| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hidden_list** | **BOOLEAN**| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **BOOLEAN**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **String**| A helpful note to users for this column | [optional] 
 **relationship_type** | **String**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **related_table** | **String**| The table name this column is related to (only used when storing relational data) | [optional] 
 **junction_table** | **String**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junction_key_left** | **String**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junction_key_right** | **String**| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **update_row**
> update_row(table_idrow_idcustom_data)

Update row

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::TablesApi.new

table_id = "table_id_example" # String | ID of table to return rows from

row_id = 56 # Integer | ID of row to return from rows

custom_data = "custom_data_example" # String | Data based on your specific schema eg: active=1&title=LoremIpsum


begin
  #Update row
  api_instance.update_row(table_idrow_idcustom_data)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling TablesApi->update_row: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **table_id** | **String**| ID of table to return rows from | 
 **row_id** | **Integer**| ID of row to return from rows | 
 **custom_data** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



