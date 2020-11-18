# \UtilsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetHash**](UtilsApi.md#GetHash) | **Post** /hash | Get a hashed value
[**GetRandom**](UtilsApi.md#GetRandom) | **Post** /random | Returns random alphanumeric string


# **GetHash**
> GetHash(ctx, optional)
Get a hashed value

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **string_** | **string**| The string to be hashed | 
 **hasher** | **string**| The hasher used to hash the given string | [default to core]
 **options** | **string**| The hasher options | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetRandom**
> GetRandom(ctx, optional)
Returns random alphanumeric string

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
 **optional** | **map[string]interface{}** | optional parameters | nil if no parameters

### Optional Parameters
Optional parameters are passed through a map[string]interface{}.

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **length** | **string**| Integer(String) for length of random string | 

### Return type

 (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

