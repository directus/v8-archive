# \MessagesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetMessage**](MessagesApi.md#GetMessage) | **Get** /messages/{messageId} | Returns specific message
[**GetMessages**](MessagesApi.md#GetMessages) | **Get** /messages/self | Returns messages


# **GetMessage**
> GetMessage GetMessage(ctx, messageId)
Returns specific message

### Required Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **ctx** | **context.Context** | context containing the authentication | nil if no authentication
  **messageId** | **int32**| ID of message to return | 

### Return type

[**GetMessage**](GetMessage.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **GetMessages**
> GetMessages GetMessages(ctx, )
Returns messages

### Required Parameters
This endpoint does not need any parameter.

### Return type

[**GetMessages**](GetMessages.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

