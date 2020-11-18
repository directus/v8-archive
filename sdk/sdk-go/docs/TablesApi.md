# \TablesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddColumn**](TablesApi.md#AddColumn) | **Post** /tables/{tableId}/columns | Create a column in a given table
[**AddRow**](TablesApi.md#AddRow) | **Post** /tables/{tableId}/rows | Add a new row
[**AddTable**](TablesApi.md#AddTable) | **Post** /tables | Add a new table
[**DeleteColumn**](TablesApi.md#DeleteColumn) | **Delete** /tables/{tableId}/columns/{columnName} | Delete row
[**DeleteRow**](TablesApi.md#DeleteRow) | **Delete** /tables/{tableId}/rows/{rowId} | Delete row
[**DeleteTable**](TablesApi.md#DeleteTable) | **Delete** /tables/{tableId} | Delete Table
[**GetTable**](TablesApi.md#GetTable) | **Get** /tables/{tableId} | Returns specific table
[**GetTableColumn**](TablesApi.md#GetTableColumn) | **Get** /tables/{tableId}/columns/{columnName} | Returns specific table column
[**GetTableColumns**](TablesApi.md#GetTableColumns) | **Get** /tables/{tableId}/columns | Returns table columns
[**GetTableRow**](TablesApi.md#GetTableRow) | **Get** /tables/{tableId}/rows/{rowId} | Returns specific table row
[**GetTableRows**](TablesApi.md#GetTableRows) | **Get** /tables/{tableId}/rows | Returns table rows
[**GetTables**](TablesApi.md#GetTables) | **Get** /tables | Returns tables
[**UpdateColumn**](TablesApi.md#UpdateColumn) | **Put** /tables/{tableId}/columns/{columnName} | Update column
[**UpdateRow**](TablesApi.md#UpdateRow) | **Put** /tables/{tableId}/rows/{rowId} | Update row


# **AddColumn**
> AddColumn(ctx, tableId, optional)
Create a column in a given table

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **tableName** | **string**| Name of table to add | 
 **columnName** | **string**| The unique name of the column to create | 
 **type_** | **string**| The datatype of the column, eg: INT | 
 **ui** | **string**| The Directus Interface to use for this column | 
 **hiddenInput** | **bool**| Whether the column will be hidden (globally) on the Edit Item page | 
 **hiddenList** | **bool**| Whether the column will be hidden (globally) on the Item Listing page | 
 **required** | **bool**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | 
 **sort** | **int32**| The sort order of the column used to override the column order in the schema | 
 **comment** | **string**| A helpful note to users for this column | 
 **relationshipType** | **string**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | 
 **relatedTable** | **string**| The table name this column is related to (only used when storing relational data) | 
 **junctionTable** | **string**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | 
 **junctionKeyLeft** | **string**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | 
 **junctionKeyRight** | **string**| The column name in junction that is related to the related table (only used when storing relational data) | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **AddRow**
> AddRow(ctx, tableId, customData)
Add a new row

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **AddTable**
> AddTable(ctx, optional)
Add a new table

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Name of table to add | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **DeleteColumn**
> DeleteColumn(ctx, tableId, columnName)
Delete row

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **columnName** | **string**| Name of column to return | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **DeleteRow**
> DeleteRow(ctx, tableId, rowId)
Delete row

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **rowId** | **int32**| ID of row to return from rows | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **DeleteTable**
> DeleteTable(ctx, tableId)
Delete Table

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTable**
> GetTable GetTable(ctx, tableId)
Returns specific table

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTable**](GetTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTableColumn**
> GetTableColumn GetTableColumn(ctx, tableId, columnName)
Returns specific table column

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **columnName** | **string**| Name of column to return | 

### Return type

[**GetTableColumn**](GetTableColumn.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTableColumns**
> GetTableColumns GetTableColumns(ctx, tableId)
Returns table columns

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTableColumns**](GetTableColumns.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTableRow**
> GetTableRow GetTableRow(ctx, tableId, rowId)
Returns specific table row

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **rowId** | **int32**| ID of row to return from rows | 

### Return type

[**GetTableRow**](GetTableRow.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTableRows**
> GetTableRows GetTableRows(ctx, tableId)
Returns table rows

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTableRows**](GetTableRows.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetTables**
> GetTables GetTables(ctx, )
Returns tables

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetTables**](GetTables.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **UpdateColumn**
> UpdateColumn(ctx, tableId, columnName, optional)
Update column

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **columnName** | **string**| Name of column to return | 
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **columnName** | **string**| Name of column to return | 
 **dataType** | **string**| The datatype of the column, eg: INT | 
 **ui** | **string**| The Directus Interface to use for this column | 
 **hiddenInput** | **bool**| Whether the column will be hidden (globally) on the Edit Item page | 
 **hiddenList** | **bool**| Whether the column will be hidden (globally) on the Item Listing page | 
 **required** | **bool**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | 
 **sort** | **int32**| The sort order of the column used to override the column order in the schema | 
 **comment** | **string**| A helpful note to users for this column | 
 **relationshipType** | **string**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | 
 **relatedTable** | **string**| The table name this column is related to (only used when storing relational data) | 
 **junctionTable** | **string**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | 
 **junctionKeyLeft** | **string**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | 
 **junctionKeyRight** | **string**| The column name in junction that is related to the related table (only used when storing relational data) | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **UpdateRow**
> UpdateRow(ctx, tableId, rowId, customData)
Update row

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 
  **rowId** | **int32**| ID of row to return from rows | 
  **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

