# MessagesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getMessage**](MessagesApi.md#getMessage) | **GET** /messages/{messageId} | Returns specific message
[**getMessages**](MessagesApi.md#getMessages) | **GET** /messages/self | Returns messages


<a name="getMessage"></a>
# **getMessage**
> GetMessage getMessage(messageId)

Returns specific message

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.MessagesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

MessagesApi apiInstance = new MessagesApi();
Integer messageId = 56; // Integer | ID of message to return
try {
    GetMessage result = apiInstance.getMessage(messageId);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling MessagesApi#getMessage");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **messageId** | **Integer**| ID of message to return |

### Return type

[**GetMessage**](GetMessage.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getMessages"></a>
# **getMessages**
> GetMessages getMessages()

Returns messages

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.MessagesApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

MessagesApi apiInstance = new MessagesApi();
try {
    GetMessages result = apiInstance.getMessages();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling MessagesApi#getMessages");
    e.printStackTrace();
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetMessages**](GetMessages.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

