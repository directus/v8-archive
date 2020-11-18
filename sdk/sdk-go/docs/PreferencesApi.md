# \PreferencesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetPreferences**](PreferencesApi.md#GetPreferences) | **Get** /tables/{tableId}/preferences | Returns table preferences
[**UpdatePreferences**](PreferencesApi.md#UpdatePreferences) | **Put** /tables/{tableId}/preferences | Update table preferences


# **GetPreferences**
> GetPreferences GetPreferences(ctx, tableId)
Returns table preferences

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **tableId** | **string**| ID of table to return rows from | 

### Return type

[**GetPreferences**](GetPreferences.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **UpdatePreferences**
> UpdatePreferences(ctx, tableId, optional)
Update table preferences

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
 **id** | **string**| Preference&#39;s Unique Identification number | 
 **tableName** | **string**| Name of table to add | 
 **columnsVisible** | **string**| List of visible columns, separated by commas | 
 **sort** | **int32**| The sort order of the column used to override the column order in the schema | 
 **sortOrder** | **string**| Sort Order (ASC&#x3D;Ascending or DESC&#x3D;Descending) | 
 **status** | **string**| List of status values. separated by comma | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

