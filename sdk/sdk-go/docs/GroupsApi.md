# \GroupsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddGroup**](GroupsApi.md#AddGroup) | **Post** /groups | Add a new group
[**AddPrivilege**](GroupsApi.md#AddPrivilege) | **Post** /privileges/{groupId} | Create new table privileges for the specified user group
[**GetGroup**](GroupsApi.md#GetGroup) | **Get** /groups/{groupId} | Returns specific group
[**GetGroups**](GroupsApi.md#GetGroups) | **Get** /groups | Returns groups
[**GetPrivileges**](GroupsApi.md#GetPrivileges) | **Get** /privileges/{groupId} | Returns group privileges
[**GetPrivilegesForTable**](GroupsApi.md#GetPrivilegesForTable) | **Get** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
[**UpdatePrivileges**](GroupsApi.md#UpdatePrivileges) | **Put** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId


# **AddGroup**
> AddGroup(ctx, optional)
Add a new group

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Name of group to add | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **AddPrivilege**
> AddPrivilege(ctx, groupId, optional)
Create new table privileges for the specified user group

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **groupId** | **string**| ID of group to return | 
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 
 **id** | **int32**| Privilege&#39;s Unique Identification number | 
 **tableName** | **string**| Name of table to add | 
 **allowAdd** | **int32**| Permission to add/create entries in the table (See values below) | 
 **allowEdit** | **int32**| Permission to edit/update entries in the table (See values below) | 
 **allowDelete** | **int32**| Permission to delete/remove entries in the table (See values below) | 
 **allowView** | **int32**| Permission to view/read entries in the table (See values below) | 
 **allowAlter** | **int32**| Permission to add/create entries in the table (See values below) | 
 **navListed** | **bool**| If the table should be visible in the sidebar for this user group | 
 **readFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t view (read) | 
 **writeFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t edit (update) | 
 **statusId** | **string**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetGroup**
> GetGroup GetGroup(ctx, groupId)
Returns specific group

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **groupId** | **string**| ID of group to return | 

### Return type

[**GetGroup**](GetGroup.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetGroups**
> GetGroups GetGroups(ctx, )
Returns groups

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetGroups**](GetGroups.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetPrivileges**
> GetPrivileges GetPrivileges(ctx, groupId)
Returns group privileges

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **groupId** | **string**| ID of group to return | 

### Return type

[**GetPrivileges**](GetPrivileges.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetPrivilegesForTable**
> GetPrivilegesForTable GetPrivilegesForTable(ctx, groupId, tableNameOrPrivilegeId)
Returns group privileges by tableName

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **groupId** | **string**| ID of group to return | 
  **tableNameOrPrivilegeId** | **string**| ID of privileges or Table Name to use | 

### Return type

[**GetPrivilegesForTable**](GetPrivilegesForTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **UpdatePrivileges**
> UpdatePrivileges(ctx, groupId, tableNameOrPrivilegeId, optional)
Update privileges by privilegeId

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **groupId** | **string**| ID of group to return | 
  **tableNameOrPrivilegeId** | **string**| ID of privileges or Table Name to use | 
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 
 **tableNameOrPrivilegeId** | **string**| ID of privileges or Table Name to use | 
 **privilegesId** | **string**| ubique privilege ID | 
 **groupId2** | **string**| ID of group to return | 
 **tableName** | **string**| Name of table to add | 
 **allowAdd** | **int32**| Permission to add/create entries in the table (See values below) | 
 **allowEdit** | **int32**| Permission to edit/update entries in the table (See values below) | 
 **allowDelete** | **int32**| Permission to delete/remove entries in the table (See values below) | 
 **allowView** | **int32**| Permission to view/read entries in the table (See values below) | 
 **allowAlter** | **int32**| Permission to add/create entries in the table (See values below) | 
 **navListed** | **bool**| If the table should be visible in the sidebar for this user group | 
 **readFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t view (read) | 
 **writeFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t edit (update) | 
 **statusId** | **string**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

