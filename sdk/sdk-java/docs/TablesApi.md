# TablesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addColumn**](TablesApi.md#addColumn) | **POST** /tables/{tableId}/columns | Create a column in a given table
[**addRow**](TablesApi.md#addRow) | **POST** /tables/{tableId}/rows | Add a new row
[**addTable**](TablesApi.md#addTable) | **POST** /tables | Add a new table
[**deleteColumn**](TablesApi.md#deleteColumn) | **DELETE** /tables/{tableId}/columns/{columnName} | Delete row
[**deleteRow**](TablesApi.md#deleteRow) | **DELETE** /tables/{tableId}/rows/{rowId} | Delete row
[**deleteTable**](TablesApi.md#deleteTable) | **DELETE** /tables/{tableId} | Delete Table
[**getTable**](TablesApi.md#getTable) | **GET** /tables/{tableId} | Returns specific table
[**getTableColumn**](TablesApi.md#getTableColumn) | **GET** /tables/{tableId}/columns/{columnName} | Returns specific table column
[**getTableColumns**](TablesApi.md#getTableColumns) | **GET** /tables/{tableId}/columns | Returns table columns
[**getTableRow**](TablesApi.md#getTableRow) | **GET** /tables/{tableId}/rows/{rowId} | Returns specific table row
[**getTableRows**](TablesApi.md#getTableRows) | **GET** /tables/{tableId}/rows | Returns table rows
[**getTables**](TablesApi.md#getTables) | **GET** /tables | Returns tables
[**updateColumn**](TablesApi.md#updateColumn) | **PUT** /tables/{tableId}/columns/{columnName} | Update column
[**updateRow**](TablesApi.md#updateRow) | **PUT** /tables/{tableId}/rows/{rowId} | Update row


<a name="addColumn"></a>
# **addColumn**
> addColumn(tableId, tableName, columnName, type, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight)

Create a column in a given table

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String tableName = "tableName_example"; // String | Name of table to add
String columnName = "columnName_example"; // String | The unique name of the column to create
String type = "type_example"; // String | The datatype of the column, eg: INT
String ui = "ui_example"; // String | The Directus Interface to use for this column
Boolean hiddenInput = true; // Boolean | Whether the column will be hidden (globally) on the Edit Item page
Boolean hiddenList = true; // Boolean | Whether the column will be hidden (globally) on the Item Listing page
Boolean required = true; // Boolean | Whether the column is required. If required, the interface's validation function will be triggered
Integer sort = 56; // Integer | The sort order of the column used to override the column order in the schema
String comment = "comment_example"; // String | A helpful note to users for this column
String relationshipType = "relationshipType_example"; // String | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE
String relatedTable = "relatedTable_example"; // String | The table name this column is related to (only used when storing relational data)
String junctionTable = "junctionTable_example"; // String | The pivot/junction table that joins the column's table with the related table (only used when storing relational data)
String junctionKeyLeft = "junctionKeyLeft_example"; // String | The column name in junction that is related to the column's table (only used when storing relational data)
String junctionKeyRight = "junctionKeyRight_example"; // String | The column name in junction that is related to the related table (only used when storing relational data)
try {
    apiInstance.addColumn(tableId, tableName, columnName, type, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#addColumn");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **tableName** | **String**| Name of table to add | [optional]
 **columnName** | **String**| The unique name of the column to create | [optional]
 **type** | **String**| The datatype of the column, eg: INT | [optional]
 **ui** | **String**| The Directus Interface to use for this column | [optional]
 **hiddenInput** | **Boolean**| Whether the column will be hidden (globally) on the Edit Item page | [optional]
 **hiddenList** | **Boolean**| Whether the column will be hidden (globally) on the Item Listing page | [optional]
 **required** | **Boolean**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional]
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional]
 **comment** | **String**| A helpful note to users for this column | [optional]
 **relationshipType** | **String**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional]
 **relatedTable** | **String**| The table name this column is related to (only used when storing relational data) | [optional]
 **junctionTable** | **String**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional]
 **junctionKeyLeft** | **String**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional]
 **junctionKeyRight** | **String**| The column name in junction that is related to the related table (only used when storing relational data) | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

<a name="addRow"></a>
# **addRow**
> addRow(tableId, customData)

Add a new row

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String customData = "customData_example"; // String | Data based on your specific schema eg: active=1&title=LoremIpsum
try {
    apiInstance.addRow(tableId, customData);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#addRow");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **customData** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="addTable"></a>
# **addTable**
> addTable(name)

Add a new table

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String name = "name_example"; // String | Name of table to add
try {
    apiInstance.addTable(name);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#addTable");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **String**| Name of table to add | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

<a name="deleteColumn"></a>
# **deleteColumn**
> deleteColumn(tableId, columnName)

Delete row

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String columnName = "columnName_example"; // String | Name of column to return
try {
    apiInstance.deleteColumn(tableId, columnName);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#deleteColumn");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **columnName** | **String**| Name of column to return |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="deleteRow"></a>
# **deleteRow**
> deleteRow(tableId, rowId)

Delete row

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
Integer rowId = 56; // Integer | ID of row to return from rows
try {
    apiInstance.deleteRow(tableId, rowId);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#deleteRow");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **rowId** | **Integer**| ID of row to return from rows |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="deleteTable"></a>
# **deleteTable**
> deleteTable(tableId)

Delete Table

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
try {
    apiInstance.deleteTable(tableId);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#deleteTable");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="getTable"></a>
# **getTable**
> GetTable getTable(tableId)

Returns specific table

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
try {
    GetTable result = apiInstance.getTable(tableId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTable");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |

### Return type

[**GetTable**](GetTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getTableColumn"></a>
# **getTableColumn**
> GetTableColumn getTableColumn(tableId, columnName)

Returns specific table column

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String columnName = "columnName_example"; // String | Name of column to return
try {
    GetTableColumn result = apiInstance.getTableColumn(tableId, columnName);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTableColumn");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **columnName** | **String**| Name of column to return |

### Return type

[**GetTableColumn**](GetTableColumn.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getTableColumns"></a>
# **getTableColumns**
> GetTableColumns getTableColumns(tableId)

Returns table columns

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
try {
    GetTableColumns result = apiInstance.getTableColumns(tableId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTableColumns");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |

### Return type

[**GetTableColumns**](GetTableColumns.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getTableRow"></a>
# **getTableRow**
> GetTableRow getTableRow(tableId, rowId)

Returns specific table row

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
Integer rowId = 56; // Integer | ID of row to return from rows
try {
    GetTableRow result = apiInstance.getTableRow(tableId, rowId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTableRow");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **rowId** | **Integer**| ID of row to return from rows |

### Return type

[**GetTableRow**](GetTableRow.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getTableRows"></a>
# **getTableRows**
> GetTableRows getTableRows(tableId)

Returns table rows

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
try {
    GetTableRows result = apiInstance.getTableRows(tableId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTableRows");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |

### Return type

[**GetTableRows**](GetTableRows.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getTables"></a>
# **getTables**
> GetTables getTables()

Returns tables

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
try {
    GetTables result = apiInstance.getTables();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#getTables");
    e.printStackTrace();
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

<a name="updateColumn"></a>
# **updateColumn**
> updateColumn(tableId, columnName, dataType, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight)

Update column

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
String columnName = "columnName_example"; // String | Name of column to return
String dataType = "dataType_example"; // String | The datatype of the column, eg: INT
String ui = "ui_example"; // String | The Directus Interface to use for this column
Boolean hiddenInput = true; // Boolean | Whether the column will be hidden (globally) on the Edit Item page
Boolean hiddenList = true; // Boolean | Whether the column will be hidden (globally) on the Item Listing page
Boolean required = true; // Boolean | Whether the column is required. If required, the interface's validation function will be triggered
Integer sort = 56; // Integer | The sort order of the column used to override the column order in the schema
String comment = "comment_example"; // String | A helpful note to users for this column
String relationshipType = "relationshipType_example"; // String | The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE
String relatedTable = "relatedTable_example"; // String | The table name this column is related to (only used when storing relational data)
String junctionTable = "junctionTable_example"; // String | The pivot/junction table that joins the column's table with the related table (only used when storing relational data)
String junctionKeyLeft = "junctionKeyLeft_example"; // String | The column name in junction that is related to the column's table (only used when storing relational data)
String junctionKeyRight = "junctionKeyRight_example"; // String | The column name in junction that is related to the related table (only used when storing relational data)
try {
    apiInstance.updateColumn(tableId, columnName, dataType, ui, hiddenInput, hiddenList, required, sort, comment, relationshipType, relatedTable, junctionTable, junctionKeyLeft, junctionKeyRight);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#updateColumn");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **columnName** | **String**| Name of column to return |
 **dataType** | **String**| The datatype of the column, eg: INT | [optional]
 **ui** | **String**| The Directus Interface to use for this column | [optional]
 **hiddenInput** | **Boolean**| Whether the column will be hidden (globally) on the Edit Item page | [optional]
 **hiddenList** | **Boolean**| Whether the column will be hidden (globally) on the Item Listing page | [optional]
 **required** | **Boolean**| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional]
 **sort** | **Integer**| The sort order of the column used to override the column order in the schema | [optional]
 **comment** | **String**| A helpful note to users for this column | [optional]
 **relationshipType** | **String**| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional]
 **relatedTable** | **String**| The table name this column is related to (only used when storing relational data) | [optional]
 **junctionTable** | **String**| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional]
 **junctionKeyLeft** | **String**| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional]
 **junctionKeyRight** | **String**| The column name in junction that is related to the related table (only used when storing relational data) | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="updateRow"></a>
# **updateRow**
> updateRow(tableId, rowId, customData)

Update row

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.TablesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

TablesApi apiInstance = new TablesApi();
String tableId = "tableId_example"; // String | ID of table to return rows from
Integer rowId = 56; // Integer | ID of row to return from rows
String customData = "customData_example"; // String | Data based on your specific schema eg: active=1&title=LoremIpsum
try {
    apiInstance.updateRow(tableId, rowId, customData);
} catch (ApiException e) {
    System.err.println("Exception when calling TablesApi#updateRow");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **String**| ID of table to return rows from |
 **rowId** | **Integer**| ID of row to return from rows |
 **customData** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

