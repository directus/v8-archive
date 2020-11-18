# DRCTSTablesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addColumn**](DRCTSTablesApi.md#addcolumn) | **POST** /tables/{tableId}/columns | Create a column in a given table
[**addRow**](DRCTSTablesApi.md#addrow) | **POST** /tables/{tableId}/rows | Add a new row
[**addTable**](DRCTSTablesApi.md#addtable) | **POST** /tables | Add a new table
[**deleteColumn**](DRCTSTablesApi.md#deletecolumn) | **DELETE** /tables/{tableId}/columns/{columnName} | Delete row
[**deleteRow**](DRCTSTablesApi.md#deleterow) | **DELETE** /tables/{tableId}/rows/{rowId} | Delete row
[**deleteTable**](DRCTSTablesApi.md#deletetable) | **DELETE** /tables/{tableId} | Delete Table
[**getTable**](DRCTSTablesApi.md#gettable) | **GET** /tables/{tableId} | Returns specific table
[**getTableColumn**](DRCTSTablesApi.md#gettablecolumn) | **GET** /tables/{tableId}/columns/{columnName} | Returns specific table column
[**getTableColumns**](DRCTSTablesApi.md#gettablecolumns) | **GET** /tables/{tableId}/columns | Returns table columns
[**getTableRow**](DRCTSTablesApi.md#gettablerow) | **GET** /tables/{tableId}/rows/{rowId} | Returns specific table row
[**getTableRows**](DRCTSTablesApi.md#gettablerows) | **GET** /tables/{tableId}/rows | Returns table rows
[**getTables**](DRCTSTablesApi.md#gettables) | **GET** /tables | Returns tables
[**updateColumn**](DRCTSTablesApi.md#updatecolumn) | **PUT** /tables/{tableId}/columns/{columnName} | Update column
[**updateRow**](DRCTSTablesApi.md#updaterow) | **PUT** /tables/{tableId}/rows/{rowId} | Update row


# **addColumn**
```objc
-(NSURLSessionTask*) addColumnWithTableId: (NSString*) tableId
    tableName: (NSString*) tableName
    columnName: (NSString*) columnName
    type: (NSString*) type
    ui: (NSString*) ui
    hiddenInput: (NSNumber*) hiddenInput
    hiddenList: (NSNumber*) hiddenList
    required: (NSNumber*) required
    sort: (NSNumber*) sort
    comment: (NSString*) comment
    relationshipType: (NSString*) relationshipType
    relatedTable: (NSString*) relatedTable
    junctionTable: (NSString*) junctionTable
    junctionKeyLeft: (NSString*) junctionKeyLeft
    junctionKeyRight: (NSString*) junctionKeyRight
        completionHandler: (void (^)(NSError* error)) handler;
```

Create a column in a given table

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* tableName = @"tableName_example"; // Name of table to add (optional)
NSString* columnName = @"columnName_example"; // The unique name of the column to create (optional)
NSString* type = @"type_example"; // The datatype of the column, eg: INT (optional)
NSString* ui = @"ui_example"; // The Directus Interface to use for this column (optional)
NSNumber* hiddenInput = @true; // Whether the column will be hidden (globally) on the Edit Item page (optional)
NSNumber* hiddenList = @true; // Whether the column will be hidden (globally) on the Item Listing page (optional)
NSNumber* required = @true; // Whether the column is required. If required, the interface's validation function will be triggered (optional)
NSNumber* sort = @56; // The sort order of the column used to override the column order in the schema (optional)
NSString* comment = @"comment_example"; // A helpful note to users for this column (optional)
NSString* relationshipType = @"relationshipType_example"; // The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional)
NSString* relatedTable = @"relatedTable_example"; // The table name this column is related to (only used when storing relational data) (optional)
NSString* junctionTable = @"junctionTable_example"; // The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional)
NSString* junctionKeyLeft = @"junctionKeyLeft_example"; // The column name in junction that is related to the column's table (only used when storing relational data) (optional)
NSString* junctionKeyRight = @"junctionKeyRight_example"; // The column name in junction that is related to the related table (only used when storing relational data) (optional)

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Create a column in a given table
[apiInstance addColumnWithTableId:tableId
              tableName:tableName
              columnName:columnName
              type:type
              ui:ui
              hiddenInput:hiddenInput
              hiddenList:hiddenList
              required:required
              sort:sort
              comment:comment
              relationshipType:relationshipType
              relatedTable:relatedTable
              junctionTable:junctionTable
              junctionKeyLeft:junctionKeyLeft
              junctionKeyRight:junctionKeyRight
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->addColumn: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **tableName** | **NSString***| Name of table to add | [optional] 
 **columnName** | **NSString***| The unique name of the column to create | [optional] 
 **type** | **NSString***| The datatype of the column, eg: INT | [optional] 
 **ui** | **NSString***| The Directus Interface to use for this column | [optional] 
 **hiddenInput** | **NSNumber***| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hiddenList** | **NSNumber***| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **NSNumber***| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **NSNumber***| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **NSString***| A helpful note to users for this column | [optional] 
 **relationshipType** | **NSString***| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **relatedTable** | **NSString***| The table name this column is related to (only used when storing relational data) | [optional] 
 **junctionTable** | **NSString***| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junctionKeyLeft** | **NSString***| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junctionKeyRight** | **NSString***| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **addRow**
```objc
-(NSURLSessionTask*) addRowWithTableId: (NSString*) tableId
    customData: (NSString*) customData
        completionHandler: (void (^)(NSError* error)) handler;
```

Add a new row

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* customData = customData_example; // Data based on your specific schema eg: active=1&title=LoremIpsum

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Add a new row
[apiInstance addRowWithTableId:tableId
              customData:customData
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->addRow: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **customData** | **NSString***| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **addTable**
```objc
-(NSURLSessionTask*) addTableWithName: (NSString*) name
        completionHandler: (void (^)(NSError* error)) handler;
```

Add a new table

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* name = @"name_example"; // Name of table to add (optional)

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Add a new table
[apiInstance addTableWithName:name
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->addTable: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **NSString***| Name of table to add | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json, application/xml

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **deleteColumn**
```objc
-(NSURLSessionTask*) deleteColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
        completionHandler: (void (^)(NSError* error)) handler;
```

Delete row

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* columnName = @"columnName_example"; // Name of column to return

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Delete row
[apiInstance deleteColumnWithTableId:tableId
              columnName:columnName
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->deleteColumn: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **columnName** | **NSString***| Name of column to return | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **deleteRow**
```objc
-(NSURLSessionTask*) deleteRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
        completionHandler: (void (^)(NSError* error)) handler;
```

Delete row

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSNumber* rowId = @56; // ID of row to return from rows

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Delete row
[apiInstance deleteRowWithTableId:tableId
              rowId:rowId
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->deleteRow: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **rowId** | **NSNumber***| ID of row to return from rows | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **deleteTable**
```objc
-(NSURLSessionTask*) deleteTableWithTableId: (NSString*) tableId
        completionHandler: (void (^)(NSError* error)) handler;
```

Delete Table

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Delete Table
[apiInstance deleteTableWithTableId:tableId
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->deleteTable: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTable**
```objc
-(NSURLSessionTask*) getTableWithTableId: (NSString*) tableId
        completionHandler: (void (^)(DRCTSGetTable* output, NSError* error)) handler;
```

Returns specific table

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns specific table
[apiInstance getTableWithTableId:tableId
          completionHandler: ^(DRCTSGetTable* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTable: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 

### Return type

[**DRCTSGetTable***](DRCTSGetTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTableColumn**
```objc
-(NSURLSessionTask*) getTableColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
        completionHandler: (void (^)(DRCTSGetTableColumn* output, NSError* error)) handler;
```

Returns specific table column

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* columnName = @"columnName_example"; // Name of column to return

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns specific table column
[apiInstance getTableColumnWithTableId:tableId
              columnName:columnName
          completionHandler: ^(DRCTSGetTableColumn* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTableColumn: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **columnName** | **NSString***| Name of column to return | 

### Return type

[**DRCTSGetTableColumn***](DRCTSGetTableColumn.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTableColumns**
```objc
-(NSURLSessionTask*) getTableColumnsWithTableId: (NSString*) tableId
        completionHandler: (void (^)(DRCTSGetTableColumns* output, NSError* error)) handler;
```

Returns table columns

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns table columns
[apiInstance getTableColumnsWithTableId:tableId
          completionHandler: ^(DRCTSGetTableColumns* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTableColumns: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 

### Return type

[**DRCTSGetTableColumns***](DRCTSGetTableColumns.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTableRow**
```objc
-(NSURLSessionTask*) getTableRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
        completionHandler: (void (^)(DRCTSGetTableRow* output, NSError* error)) handler;
```

Returns specific table row

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSNumber* rowId = @56; // ID of row to return from rows

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns specific table row
[apiInstance getTableRowWithTableId:tableId
              rowId:rowId
          completionHandler: ^(DRCTSGetTableRow* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTableRow: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **rowId** | **NSNumber***| ID of row to return from rows | 

### Return type

[**DRCTSGetTableRow***](DRCTSGetTableRow.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTableRows**
```objc
-(NSURLSessionTask*) getTableRowsWithTableId: (NSString*) tableId
        completionHandler: (void (^)(DRCTSGetTableRows* output, NSError* error)) handler;
```

Returns table rows

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns table rows
[apiInstance getTableRowsWithTableId:tableId
          completionHandler: ^(DRCTSGetTableRows* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTableRows: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 

### Return type

[**DRCTSGetTableRows***](DRCTSGetTableRows.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getTables**
```objc
-(NSURLSessionTask*) getTablesWithCompletionHandler: 
        (void (^)(DRCTSGetTables* output, NSError* error)) handler;
```

Returns tables

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Returns tables
[apiInstance getTablesWithCompletionHandler: 
          ^(DRCTSGetTables* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->getTables: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetTables***](DRCTSGetTables.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **updateColumn**
```objc
-(NSURLSessionTask*) updateColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
    dataType: (NSString*) dataType
    ui: (NSString*) ui
    hiddenInput: (NSNumber*) hiddenInput
    hiddenList: (NSNumber*) hiddenList
    required: (NSNumber*) required
    sort: (NSNumber*) sort
    comment: (NSString*) comment
    relationshipType: (NSString*) relationshipType
    relatedTable: (NSString*) relatedTable
    junctionTable: (NSString*) junctionTable
    junctionKeyLeft: (NSString*) junctionKeyLeft
    junctionKeyRight: (NSString*) junctionKeyRight
        completionHandler: (void (^)(NSError* error)) handler;
```

Update column

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* columnName = @"columnName_example"; // Name of column to return
NSString* dataType = @"dataType_example"; // The datatype of the column, eg: INT (optional)
NSString* ui = @"ui_example"; // The Directus Interface to use for this column (optional)
NSNumber* hiddenInput = @true; // Whether the column will be hidden (globally) on the Edit Item page (optional)
NSNumber* hiddenList = @true; // Whether the column will be hidden (globally) on the Item Listing page (optional)
NSNumber* required = @true; // Whether the column is required. If required, the interface's validation function will be triggered (optional)
NSNumber* sort = @56; // The sort order of the column used to override the column order in the schema (optional)
NSString* comment = @"comment_example"; // A helpful note to users for this column (optional)
NSString* relationshipType = @"relationshipType_example"; // The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional)
NSString* relatedTable = @"relatedTable_example"; // The table name this column is related to (only used when storing relational data) (optional)
NSString* junctionTable = @"junctionTable_example"; // The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional)
NSString* junctionKeyLeft = @"junctionKeyLeft_example"; // The column name in junction that is related to the column's table (only used when storing relational data) (optional)
NSString* junctionKeyRight = @"junctionKeyRight_example"; // The column name in junction that is related to the related table (only used when storing relational data) (optional)

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Update column
[apiInstance updateColumnWithTableId:tableId
              columnName:columnName
              dataType:dataType
              ui:ui
              hiddenInput:hiddenInput
              hiddenList:hiddenList
              required:required
              sort:sort
              comment:comment
              relationshipType:relationshipType
              relatedTable:relatedTable
              junctionTable:junctionTable
              junctionKeyLeft:junctionKeyLeft
              junctionKeyRight:junctionKeyRight
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->updateColumn: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **columnName** | **NSString***| Name of column to return | 
 **dataType** | **NSString***| The datatype of the column, eg: INT | [optional] 
 **ui** | **NSString***| The Directus Interface to use for this column | [optional] 
 **hiddenInput** | **NSNumber***| Whether the column will be hidden (globally) on the Edit Item page | [optional] 
 **hiddenList** | **NSNumber***| Whether the column will be hidden (globally) on the Item Listing page | [optional] 
 **required** | **NSNumber***| Whether the column is required. If required, the interface&#39;s validation function will be triggered | [optional] 
 **sort** | **NSNumber***| The sort order of the column used to override the column order in the schema | [optional] 
 **comment** | **NSString***| A helpful note to users for this column | [optional] 
 **relationshipType** | **NSString***| The column&#39;s relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE | [optional] 
 **relatedTable** | **NSString***| The table name this column is related to (only used when storing relational data) | [optional] 
 **junctionTable** | **NSString***| The pivot/junction table that joins the column&#39;s table with the related table (only used when storing relational data) | [optional] 
 **junctionKeyLeft** | **NSString***| The column name in junction that is related to the column&#39;s table (only used when storing relational data) | [optional] 
 **junctionKeyRight** | **NSString***| The column name in junction that is related to the related table (only used when storing relational data) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **updateRow**
```objc
-(NSURLSessionTask*) updateRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
    customData: (NSString*) customData
        completionHandler: (void (^)(NSError* error)) handler;
```

Update row

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSNumber* rowId = @56; // ID of row to return from rows
NSString* customData = customData_example; // Data based on your specific schema eg: active=1&title=LoremIpsum

DRCTSTablesApi*apiInstance = [[DRCTSTablesApi alloc] init];

// Update row
[apiInstance updateRowWithTableId:tableId
              rowId:rowId
              customData:customData
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSTablesApi->updateRow: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **rowId** | **NSNumber***| ID of row to return from rows | 
 **customData** | **NSString***| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

