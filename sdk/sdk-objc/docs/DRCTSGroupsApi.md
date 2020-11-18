# DRCTSGroupsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addGroup**](DRCTSGroupsApi.md#addgroup) | **POST** /groups | Add a new group
[**addPrivilege**](DRCTSGroupsApi.md#addprivilege) | **POST** /privileges/{groupId} | Create new table privileges for the specified user group
[**getGroup**](DRCTSGroupsApi.md#getgroup) | **GET** /groups/{groupId} | Returns specific group
[**getGroups**](DRCTSGroupsApi.md#getgroups) | **GET** /groups | Returns groups
[**getPrivileges**](DRCTSGroupsApi.md#getprivileges) | **GET** /privileges/{groupId} | Returns group privileges
[**getPrivilegesForTable**](DRCTSGroupsApi.md#getprivilegesfortable) | **GET** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
[**updatePrivileges**](DRCTSGroupsApi.md#updateprivileges) | **PUT** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId


# **addGroup**
```objc
-(NSURLSessionTask*) addGroupWithName: (NSString*) name
        completionHandler: (void (^)(NSError* error)) handler;
```

Add a new group

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* name = @"name_example"; // Name of group to add (optional)

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Add a new group
[apiInstance addGroupWithName:name
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->addGroup: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **NSString***| Name of group to add | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **addPrivilege**
```objc
-(NSURLSessionTask*) addPrivilegeWithGroupId: (NSString*) groupId
    _id: (NSNumber*) _id
    tableName: (NSString*) tableName
    allowAdd: (NSNumber*) allowAdd
    allowEdit: (NSNumber*) allowEdit
    allowDelete: (NSNumber*) allowDelete
    allowView: (NSNumber*) allowView
    allowAlter: (NSNumber*) allowAlter
    navListed: (NSNumber*) navListed
    readFieldBlacklist: (NSString*) readFieldBlacklist
    writeFieldBlacklist: (NSString*) writeFieldBlacklist
    statusId: (NSString*) statusId
        completionHandler: (void (^)(NSError* error)) handler;
```

Create new table privileges for the specified user group

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* groupId = @"groupId_example"; // ID of group to return
NSNumber* _id = @56; // Privilege's Unique Identification number (optional)
NSString* tableName = @"tableName_example"; // Name of table to add (optional)
NSNumber* allowAdd = @56; // Permission to add/create entries in the table (See values below) (optional)
NSNumber* allowEdit = @56; // Permission to edit/update entries in the table (See values below) (optional)
NSNumber* allowDelete = @56; // Permission to delete/remove entries in the table (See values below) (optional)
NSNumber* allowView = @56; // Permission to view/read entries in the table (See values below) (optional)
NSNumber* allowAlter = @56; // Permission to add/create entries in the table (See values below) (optional)
NSNumber* navListed = @true; // If the table should be visible in the sidebar for this user group (optional)
NSString* readFieldBlacklist = @"readFieldBlacklist_example"; // A CSV of column names that the group can't view (read) (optional)
NSString* writeFieldBlacklist = @"writeFieldBlacklist_example"; // A CSV of column names that the group can't edit (update) (optional)
NSString* statusId = @"statusId_example"; // State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional)

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Create new table privileges for the specified user group
[apiInstance addPrivilegeWithGroupId:groupId
              _id:_id
              tableName:tableName
              allowAdd:allowAdd
              allowEdit:allowEdit
              allowDelete:allowDelete
              allowView:allowView
              allowAlter:allowAlter
              navListed:navListed
              readFieldBlacklist:readFieldBlacklist
              writeFieldBlacklist:writeFieldBlacklist
              statusId:statusId
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->addPrivilege: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **NSString***| ID of group to return | 
 **_id** | **NSNumber***| Privilege&#39;s Unique Identification number | [optional] 
 **tableName** | **NSString***| Name of table to add | [optional] 
 **allowAdd** | **NSNumber***| Permission to add/create entries in the table (See values below) | [optional] 
 **allowEdit** | **NSNumber***| Permission to edit/update entries in the table (See values below) | [optional] 
 **allowDelete** | **NSNumber***| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allowView** | **NSNumber***| Permission to view/read entries in the table (See values below) | [optional] 
 **allowAlter** | **NSNumber***| Permission to add/create entries in the table (See values below) | [optional] 
 **navListed** | **NSNumber***| If the table should be visible in the sidebar for this user group | [optional] 
 **readFieldBlacklist** | **NSString***| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **writeFieldBlacklist** | **NSString***| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **statusId** | **NSString***| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getGroup**
```objc
-(NSURLSessionTask*) getGroupWithGroupId: (NSString*) groupId
        completionHandler: (void (^)(DRCTSGetGroup* output, NSError* error)) handler;
```

Returns specific group

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* groupId = @"groupId_example"; // ID of group to return

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Returns specific group
[apiInstance getGroupWithGroupId:groupId
          completionHandler: ^(DRCTSGetGroup* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->getGroup: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **NSString***| ID of group to return | 

### Return type

[**DRCTSGetGroup***](DRCTSGetGroup.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getGroups**
```objc
-(NSURLSessionTask*) getGroupsWithCompletionHandler: 
        (void (^)(DRCTSGetGroups* output, NSError* error)) handler;
```

Returns groups

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Returns groups
[apiInstance getGroupsWithCompletionHandler: 
          ^(DRCTSGetGroups* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->getGroups: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetGroups***](DRCTSGetGroups.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getPrivileges**
```objc
-(NSURLSessionTask*) getPrivilegesWithGroupId: (NSString*) groupId
        completionHandler: (void (^)(DRCTSGetPrivileges* output, NSError* error)) handler;
```

Returns group privileges

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* groupId = @"groupId_example"; // ID of group to return

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Returns group privileges
[apiInstance getPrivilegesWithGroupId:groupId
          completionHandler: ^(DRCTSGetPrivileges* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->getPrivileges: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **NSString***| ID of group to return | 

### Return type

[**DRCTSGetPrivileges***](DRCTSGetPrivileges.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getPrivilegesForTable**
```objc
-(NSURLSessionTask*) getPrivilegesForTableWithGroupId: (NSString*) groupId
    tableNameOrPrivilegeId: (NSString*) tableNameOrPrivilegeId
        completionHandler: (void (^)(DRCTSGetPrivilegesForTable* output, NSError* error)) handler;
```

Returns group privileges by tableName

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* groupId = @"groupId_example"; // ID of group to return
NSString* tableNameOrPrivilegeId = @"tableNameOrPrivilegeId_example"; // ID of privileges or Table Name to use

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Returns group privileges by tableName
[apiInstance getPrivilegesForTableWithGroupId:groupId
              tableNameOrPrivilegeId:tableNameOrPrivilegeId
          completionHandler: ^(DRCTSGetPrivilegesForTable* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->getPrivilegesForTable: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **NSString***| ID of group to return | 
 **tableNameOrPrivilegeId** | **NSString***| ID of privileges or Table Name to use | 

### Return type

[**DRCTSGetPrivilegesForTable***](DRCTSGetPrivilegesForTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **updatePrivileges**
```objc
-(NSURLSessionTask*) updatePrivilegesWithGroupId: (NSString*) groupId
    tableNameOrPrivilegeId: (NSString*) tableNameOrPrivilegeId
    privilegesId: (NSString*) privilegesId
    groupId2: (NSString*) groupId2
    tableName: (NSString*) tableName
    allowAdd: (NSNumber*) allowAdd
    allowEdit: (NSNumber*) allowEdit
    allowDelete: (NSNumber*) allowDelete
    allowView: (NSNumber*) allowView
    allowAlter: (NSNumber*) allowAlter
    navListed: (NSNumber*) navListed
    readFieldBlacklist: (NSString*) readFieldBlacklist
    writeFieldBlacklist: (NSString*) writeFieldBlacklist
    statusId: (NSString*) statusId
        completionHandler: (void (^)(NSError* error)) handler;
```

Update privileges by privilegeId

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* groupId = @"groupId_example"; // ID of group to return
NSString* tableNameOrPrivilegeId = @"tableNameOrPrivilegeId_example"; // ID of privileges or Table Name to use
NSString* privilegesId = @"privilegesId_example"; // ubique privilege ID (optional)
NSString* groupId2 = @"groupId_example"; // ID of group to return (optional)
NSString* tableName = @"tableName_example"; // Name of table to add (optional)
NSNumber* allowAdd = @56; // Permission to add/create entries in the table (See values below) (optional)
NSNumber* allowEdit = @56; // Permission to edit/update entries in the table (See values below) (optional)
NSNumber* allowDelete = @56; // Permission to delete/remove entries in the table (See values below) (optional)
NSNumber* allowView = @56; // Permission to view/read entries in the table (See values below) (optional)
NSNumber* allowAlter = @56; // Permission to add/create entries in the table (See values below) (optional)
NSNumber* navListed = @true; // If the table should be visible in the sidebar for this user group (optional)
NSString* readFieldBlacklist = @"readFieldBlacklist_example"; // A CSV of column names that the group can't view (read) (optional)
NSString* writeFieldBlacklist = @"writeFieldBlacklist_example"; // A CSV of column names that the group can't edit (update) (optional)
NSString* statusId = @"statusId_example"; // State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional)

DRCTSGroupsApi*apiInstance = [[DRCTSGroupsApi alloc] init];

// Update privileges by privilegeId
[apiInstance updatePrivilegesWithGroupId:groupId
              tableNameOrPrivilegeId:tableNameOrPrivilegeId
              privilegesId:privilegesId
              groupId2:groupId2
              tableName:tableName
              allowAdd:allowAdd
              allowEdit:allowEdit
              allowDelete:allowDelete
              allowView:allowView
              allowAlter:allowAlter
              navListed:navListed
              readFieldBlacklist:readFieldBlacklist
              writeFieldBlacklist:writeFieldBlacklist
              statusId:statusId
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSGroupsApi->updatePrivileges: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **NSString***| ID of group to return | 
 **tableNameOrPrivilegeId** | **NSString***| ID of privileges or Table Name to use | 
 **privilegesId** | **NSString***| ubique privilege ID | [optional] 
 **groupId2** | **NSString***| ID of group to return | [optional] 
 **tableName** | **NSString***| Name of table to add | [optional] 
 **allowAdd** | **NSNumber***| Permission to add/create entries in the table (See values below) | [optional] 
 **allowEdit** | **NSNumber***| Permission to edit/update entries in the table (See values below) | [optional] 
 **allowDelete** | **NSNumber***| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allowView** | **NSNumber***| Permission to view/read entries in the table (See values below) | [optional] 
 **allowAlter** | **NSNumber***| Permission to add/create entries in the table (See values below) | [optional] 
 **navListed** | **NSNumber***| If the table should be visible in the sidebar for this user group | [optional] 
 **readFieldBlacklist** | **NSString***| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **writeFieldBlacklist** | **NSString***| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **statusId** | **NSString***| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

