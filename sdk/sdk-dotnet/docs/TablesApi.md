# IO.Directus.Api.TablesApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddColumn**](TablesApi.md#addcolumn) | **POST** /tables/{tableId}/columns | Create a column in a given table
[**AddRow**](TablesApi.md#addrow) | **POST** /tables/{tableId}/rows | Add a new row
[**AddTable**](TablesApi.md#addtable) | **POST** /tables | Add a new table
[**DeleteColumn**](TablesApi.md#deletecolumn) | **DELETE** /tables/{tableId}/columns/{columnName} | Delete row
[**DeleteRow**](TablesApi.md#deleterow) | **DELETE** /tables/{tableId}/rows/{rowId} | Delete row
[**DeleteTable**](TablesApi.md#deletetable) | **DELETE** /tables/{tableId} | Delete Table
[**GetTable**](TablesApi.md#gettable) | **GET** /tables/{tableId} | Returns specific table
[**GetTableColumn**](TablesApi.md#gettablecolumn) | **GET** /tables/{tableId}/columns/{columnName} | Returns specific table column
[**GetTableColumns**](TablesApi.md#gettablecolumns) | **GET** /tables/{tableId}/columns | Returns table columns
[**GetTableRow**](TablesApi.md#gettablerow) | **GET** /tables/{tableId}/rows/{rowId} | Returns specific table row
[**GetTableRows**](TablesApi.md#gettablerows) | **GET** /tables/{tableId}/rows | Returns table rows
[**GetTables**](TablesApi.md#gettables) | **GET** /tables | Returns tables
[**UpdateColumn**](TablesApi.md#updatecolumn) | **PUT** /tables/{tableId}/columns/{columnName} | Update column
[**UpdateRow**](TablesApi.md#updaterow) | **PUT** /tables/{tableId}/rows/{rowId} | Update row


<a name="addcolumn"></a>
# **AddColumn**
> void AddColumn (string tableId, string tableName = null, string columnName = null, string type = null, string ui = null, bool? hiddenInput = null, bool? hiddenList = null, bool? required = null, int? sort = null, string comment = null, string relationshipType = null, string relatedTable = null, string junctionTable = null, string junctionKeyLeft = null, string junctionKeyRight = null)

Create a column in a given table

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddColumnExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var tableName = tableName_example;  // string | Name of table to add (optional) 
            var columnName = columnName_example;  // string | The unique name of the column to create (optional) 
            var type = type_example;  // string | The datatype of the column, eg: INT (optional) 
            var ui = ui_example;  // string | The Directus Interface to use for this column (optional) 
            var hiddenInput = true;  // bool? | Whether the column will be hidden (globally) on the Edit Item page (optional) 
            var hiddenList = true;  // bool? | Whether the column will be hidden (globally) on the Item Listing page (optional) 
            var required = true;  // bool? | Whether the column is required. If required, the interface's validation function will be triggered (optional) 
            var sort = 56;  // int? | The sort order of the column used to override the column order in the schema (optional) 
            var comment = comment_example;  // string | A helpful note to users for this column (optional) 
            var relationshipType = relationshipType_example;  // string | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional) 
            var relatedTable = relatedTable_example;  // string | The table name this column is related to (only used when storing relational data) (optional) 
            var junctionTable = junctionTable_example;  // string | The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional) 
            var junctionKeyLeft = junctionKeyLeft_example;  // string | The column name in junction that is related to the column's table (only used when storing relational data) (optional) 
            var junctionKeyRight = junctionKeyRight_example;  // string | The column name in junction that is related to the related table (only used when storing relational data) (optional) 

            try
            {
                // Create a column in a given table
                apiInstance.AddColumn(tableId, tableName, columnName, type, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.AddColumn: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **tableName** | **string**| Name of table to add | [optional] 
 **columnName** | **string**| The unique name of the column to create | [optional] 
 **type** | **string**| The datatype of the column, eg: INT | [optional] 
 **ui** | **string**| The Directus Interface to use for this column | [optional] 
 **hiddenInput** | **bool?**| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hiddenList** | **bool?**| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **bool?**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **int?**| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **string**| A helpful note to users for this column | [optional] 
 **relationshipType** | **string**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **relatedTable** | **string**| The table name this column is related to (only used when storing relational data) | [optional] 
 **junctionTable** | **string**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junctionKeyLeft** | **string**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junctionKeyRight** | **string**| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="addrow"></a>
# **AddRow**
> void AddRow (string tableId, string customData)

Add a new row

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddRowExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var customData = customData_example;  // string | Data based on your specific schema eg: active=1&title=LoremIpsum

            try
            {
                // Add a new row
                apiInstance.AddRow(tableId, customData);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.AddRow: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="addtable"></a>
# **AddTable**
> void AddTable (string name = null)

Add a new table

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddTableExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var name = name_example;  // string | Name of table to add (optional) 

            try
            {
                // Add a new table
                apiInstance.AddTable(name);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.AddTable: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Name of table to add | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="deletecolumn"></a>
# **DeleteColumn**
> void DeleteColumn (string tableId, string columnName)

Delete row

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class DeleteColumnExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var columnName = columnName_example;  // string | Name of column to return

            try
            {
                // Delete row
                apiInstance.DeleteColumn(tableId, columnName);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.DeleteColumn: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **columnName** | **string**| Name of column to return | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="deleterow"></a>
# **DeleteRow**
> void DeleteRow (string tableId, int? rowId)

Delete row

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class DeleteRowExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var rowId = 56;  // int? | ID of row to return from rows

            try
            {
                // Delete row
                apiInstance.DeleteRow(tableId, rowId);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.DeleteRow: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **rowId** | **int?**| ID of row to return from rows | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="deletetable"></a>
# **DeleteTable**
> void DeleteTable (string tableId)

Delete Table

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class DeleteTableExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from

            try
            {
                // Delete Table
                apiInstance.DeleteTable(tableId);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.DeleteTable: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="gettable"></a>
# **GetTable**
> GetTable GetTable (string tableId)

Returns specific table

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTableExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from

            try
            {
                // Returns specific table
                GetTable result = apiInstance.GetTable(tableId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTable: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTable**](GetTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="gettablecolumn"></a>
# **GetTableColumn**
> GetTableColumn GetTableColumn (string tableId, string columnName)

Returns specific table column

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTableColumnExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var columnName = columnName_example;  // string | Name of column to return

            try
            {
                // Returns specific table column
                GetTableColumn result = apiInstance.GetTableColumn(tableId, columnName);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTableColumn: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
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

<a name="gettablecolumns"></a>
# **GetTableColumns**
> GetTableColumns GetTableColumns (string tableId)

Returns table columns

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTableColumnsExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from

            try
            {
                // Returns table columns
                GetTableColumns result = apiInstance.GetTableColumns(tableId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTableColumns: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTableColumns**](GetTableColumns.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="gettablerow"></a>
# **GetTableRow**
> GetTableRow GetTableRow (string tableId, int? rowId)

Returns specific table row

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTableRowExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var rowId = 56;  // int? | ID of row to return from rows

            try
            {
                // Returns specific table row
                GetTableRow result = apiInstance.GetTableRow(tableId, rowId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTableRow: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **rowId** | **int?**| ID of row to return from rows | 

### Return type

[**GetTableRow**](GetTableRow.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="gettablerows"></a>
# **GetTableRows**
> GetTableRows GetTableRows (string tableId)

Returns table rows

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTableRowsExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from

            try
            {
                // Returns table rows
                GetTableRows result = apiInstance.GetTableRows(tableId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTableRows: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetTableRows**](GetTableRows.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="gettables"></a>
# **GetTables**
> GetTables GetTables ()

Returns tables

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetTablesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();

            try
            {
                // Returns tables
                GetTables result = apiInstance.GetTables();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.GetTables: " + e.Message );
            }
        }
    }
}
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

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="updatecolumn"></a>
# **UpdateColumn**
> void UpdateColumn (string tableId, string columnName, string dataType = null, string ui = null, bool? hiddenInput = null, bool? hiddenList = null, bool? required = null, int? sort = null, string comment = null, string relationshipType = null, string relatedTable = null, string junctionTable = null, string junctionKeyLeft = null, string junctionKeyRight = null)

Update column

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class UpdateColumnExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var columnName = columnName_example;  // string | Name of column to return
            var dataType = dataType_example;  // string | The datatype of the column, eg: INT (optional) 
            var ui = ui_example;  // string | The Directus Interface to use for this column (optional) 
            var hiddenInput = true;  // bool? | Whether the column will be hidden (globally) on the Edit Item page (optional) 
            var hiddenList = true;  // bool? | Whether the column will be hidden (globally) on the Item Listing page (optional) 
            var required = true;  // bool? | Whether the column is required. If required, the interface's validation function will be triggered (optional) 
            var sort = 56;  // int? | The sort order of the column used to override the column order in the schema (optional) 
            var comment = comment_example;  // string | A helpful note to users for this column (optional) 
            var relationshipType = relationshipType_example;  // string | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional) 
            var relatedTable = relatedTable_example;  // string | The table name this column is related to (only used when storing relational data) (optional) 
            var junctionTable = junctionTable_example;  // string | The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional) 
            var junctionKeyLeft = junctionKeyLeft_example;  // string | The column name in junction that is related to the column's table (only used when storing relational data) (optional) 
            var junctionKeyRight = junctionKeyRight_example;  // string | The column name in junction that is related to the related table (only used when storing relational data) (optional) 

            try
            {
                // Update column
                apiInstance.UpdateColumn(tableId, columnName, dataType, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.UpdateColumn: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **columnName** | **string**| Name of column to return | 
 **dataType** | **string**| The datatype of the column, eg: INT | [optional] 
 **ui** | **string**| The Directus Interface to use for this column | [optional] 
 **hiddenInput** | **bool?**| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hiddenList** | **bool?**| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **bool?**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **int?**| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **string**| A helpful note to users for this column | [optional] 
 **relationshipType** | **string**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **relatedTable** | **string**| The table name this column is related to (only used when storing relational data) | [optional] 
 **junctionTable** | **string**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junctionKeyLeft** | **string**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junctionKeyRight** | **string**| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="updaterow"></a>
# **UpdateRow**
> void UpdateRow (string tableId, int? rowId, string customData)

Update row

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class UpdateRowExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new TablesApi();
            var tableId = tableId_example;  // string | ID of table to return rows from
            var rowId = 56;  // int? | ID of row to return from rows
            var customData = customData_example;  // string | Data based on your specific schema eg: active=1&title=LoremIpsum

            try
            {
                // Update row
                apiInstance.UpdateRow(tableId, rowId, customData);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling TablesApi.UpdateRow: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **string**| ID of table to return rows from | 
 **rowId** | **int?**| ID of row to return from rows | 
 **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

