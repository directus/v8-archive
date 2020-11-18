# GroupsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**addGroup**](GroupsApi.md#addGroup) | **POST** /groups | Add a new group
[**addPrivilege**](GroupsApi.md#addPrivilege) | **POST** /privileges/{groupId} | Create new table privileges for the specified user group
[**getGroup**](GroupsApi.md#getGroup) | **GET** /groups/{groupId} | Returns specific group
[**getGroups**](GroupsApi.md#getGroups) | **GET** /groups | Returns groups
[**getPrivileges**](GroupsApi.md#getPrivileges) | **GET** /privileges/{groupId} | Returns group privileges
[**getPrivilegesForTable**](GroupsApi.md#getPrivilegesForTable) | **GET** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
[**updatePrivileges**](GroupsApi.md#updatePrivileges) | **PUT** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId


<a name="addGroup"></a>
# **addGroup**
> addGroup(name)

Add a new group

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String name = "name_example"; // String | Name of group to add
try {
    apiInstance.addGroup(name);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#addGroup");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **String**| Name of group to add | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="addPrivilege"></a>
# **addPrivilege**
> addPrivilege(groupId, id, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId)

Create new table privileges for the specified user group

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String groupId = "groupId_example"; // String | ID of group to return
Integer id = 56; // Integer | Privilege's Unique Identification number
String tableName = "tableName_example"; // String | Name of table to add
Integer allowAdd = 56; // Integer | Permission to add/create entries in the table (See values below)
Integer allowEdit = 56; // Integer | Permission to edit/update entries in the table (See values below)
Integer allowDelete = 56; // Integer | Permission to delete/remove entries in the table (See values below)
Integer allowView = 56; // Integer | Permission to view/read entries in the table (See values below)
Integer allowAlter = 56; // Integer | Permission to add/create entries in the table (See values below)
Boolean navListed = true; // Boolean | If the table should be visible in the sidebar for this user group
String readFieldBlacklist = "readFieldBlacklist_example"; // String | A CSV of column names that the group can't view (read)
String writeFieldBlacklist = "writeFieldBlacklist_example"; // String | A CSV of column names that the group can't edit (update)
String statusId = "statusId_example"; // String | State of the record that this permissions belongs to (Draft, Active or Soft Deleted)
try {
    apiInstance.addPrivilege(groupId, id, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#addPrivilege");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **String**| ID of group to return |
 **id** | **Integer**| Privilege&#39;s Unique Identification number | [optional]
 **tableName** | **String**| Name of table to add | [optional]
 **allowAdd** | **Integer**| Permission to add/create entries in the table (See values below) | [optional]
 **allowEdit** | **Integer**| Permission to edit/update entries in the table (See values below) | [optional]
 **allowDelete** | **Integer**| Permission to delete/remove entries in the table (See values below) | [optional]
 **allowView** | **Integer**| Permission to view/read entries in the table (See values below) | [optional]
 **allowAlter** | **Integer**| Permission to add/create entries in the table (See values below) | [optional]
 **navListed** | **Boolean**| If the table should be visible in the sidebar for this user group | [optional]
 **readFieldBlacklist** | **String**| A CSV of column names that the group can&#39;t view (read) | [optional]
 **writeFieldBlacklist** | **String**| A CSV of column names that the group can&#39;t edit (update) | [optional]
 **statusId** | **String**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

<a name="getGroup"></a>
# **getGroup**
> GetGroup getGroup(groupId)

Returns specific group

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String groupId = "groupId_example"; // String | ID of group to return
try {
    GetGroup result = apiInstance.getGroup(groupId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#getGroup");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **String**| ID of group to return |

### Return type

[**GetGroup**](GetGroup.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getGroups"></a>
# **getGroups**
> GetGroups getGroups()

Returns groups

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
try {
    GetGroups result = apiInstance.getGroups();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#getGroups");
    e.printStackTrace();
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetGroups**](GetGroups.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getPrivileges"></a>
# **getPrivileges**
> GetPrivileges getPrivileges(groupId)

Returns group privileges

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String groupId = "groupId_example"; // String | ID of group to return
try {
    GetPrivileges result = apiInstance.getPrivileges(groupId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#getPrivileges");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **String**| ID of group to return |

### Return type

[**GetPrivileges**](GetPrivileges.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getPrivilegesForTable"></a>
# **getPrivilegesForTable**
> GetPrivilegesForTable getPrivilegesForTable(groupId, tableNameOrPrivilegeId)

Returns group privileges by tableName

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String groupId = "groupId_example"; // String | ID of group to return
String tableNameOrPrivilegeId = "tableNameOrPrivilegeId_example"; // String | ID of privileges or Table Name to use
try {
    GetPrivilegesForTable result = apiInstance.getPrivilegesForTable(groupId, tableNameOrPrivilegeId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#getPrivilegesForTable");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **String**| ID of group to return |
 **tableNameOrPrivilegeId** | **String**| ID of privileges or Table Name to use |

### Return type

[**GetPrivilegesForTable**](GetPrivilegesForTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="updatePrivileges"></a>
# **updatePrivileges**
> updatePrivileges(groupId, tableNameOrPrivilegeId, privilegesId, groupId2, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId)

Update privileges by privilegeId

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.GroupsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

GroupsApi apiInstance = new GroupsApi();
String groupId = "groupId_example"; // String | ID of group to return
String tableNameOrPrivilegeId = "tableNameOrPrivilegeId_example"; // String | ID of privileges or Table Name to use
String privilegesId = "privilegesId_example"; // String | ubique privilege ID
String groupId2 = "groupId_example"; // String | ID of group to return
String tableName = "tableName_example"; // String | Name of table to add
Integer allowAdd = 56; // Integer | Permission to add/create entries in the table (See values below)
Integer allowEdit = 56; // Integer | Permission to edit/update entries in the table (See values below)
Integer allowDelete = 56; // Integer | Permission to delete/remove entries in the table (See values below)
Integer allowView = 56; // Integer | Permission to view/read entries in the table (See values below)
Integer allowAlter = 56; // Integer | Permission to add/create entries in the table (See values below)
Boolean navListed = true; // Boolean | If the table should be visible in the sidebar for this user group
String readFieldBlacklist = "readFieldBlacklist_example"; // String | A CSV of column names that the group can't view (read)
String writeFieldBlacklist = "writeFieldBlacklist_example"; // String | A CSV of column names that the group can't edit (update)
String statusId = "statusId_example"; // String | State of the record that this permissions belongs to (Draft, Active or Soft Deleted)
try {
    apiInstance.updatePrivileges(groupId, tableNameOrPrivilegeId, privilegesId, groupId2, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId);
} catch (ApiException e) {
    System.err.println("Exception when calling GroupsApi#updatePrivileges");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **String**| ID of group to return |
 **tableNameOrPrivilegeId** | **String**| ID of privileges or Table Name to use |
 **privilegesId** | **String**| ubique privilege ID | [optional]
 **groupId2** | **String**| ID of group to return | [optional]
 **tableName** | **String**| Name of table to add | [optional]
 **allowAdd** | **Integer**| Permission to add/create entries in the table (See values below) | [optional]
 **allowEdit** | **Integer**| Permission to edit/update entries in the table (See values below) | [optional]
 **allowDelete** | **Integer**| Permission to delete/remove entries in the table (See values below) | [optional]
 **allowView** | **Integer**| Permission to view/read entries in the table (See values below) | [optional]
 **allowAlter** | **Integer**| Permission to add/create entries in the table (See values below) | [optional]
 **navListed** | **Boolean**| If the table should be visible in the sidebar for this user group | [optional]
 **readFieldBlacklist** | **String**| A CSV of column names that the group can&#39;t view (read) | [optional]
 **writeFieldBlacklist** | **String**| A CSV of column names that the group can&#39;t edit (update) | [optional]
 **statusId** | **String**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional]

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

