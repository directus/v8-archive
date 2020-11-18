# \SettingsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetSettings**](SettingsApi.md#GetSettings) | **Get** /settings | Returns settings
[**GetSettingsFor**](SettingsApi.md#GetSettingsFor) | **Get** /settings/{collectionName} | Returns settings for collection
[**UpdateSettings**](SettingsApi.md#UpdateSettings) | **Put** /settings/{collectionName} | Update settings


# **GetSettings**
> GetSettings GetSettings(ctx, )
Returns settings

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetSettings**](GetSettings.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetSettingsFor**
> GetSettingsFor GetSettingsFor(ctx, collectionName)
Returns settings for collection

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **collectionName** | **int32**| Name of collection to return settings for | 

### Return type

[**GetSettingsFor**](GetSettingsFor.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **UpdateSettings**
> UpdateSettings(ctx, collectionName, customData)
Update settings

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **collectionName** | **int32**| Name of collection to return settings for | 
  **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

