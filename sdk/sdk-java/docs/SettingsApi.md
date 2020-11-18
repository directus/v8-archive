# SettingsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getSettings**](SettingsApi.md#getSettings) | **GET** /settings | Returns settings
[**getSettingsFor**](SettingsApi.md#getSettingsFor) | **GET** /settings/{collectionName} | Returns settings for collection
[**updateSettings**](SettingsApi.md#updateSettings) | **PUT** /settings/{collectionName} | Update settings


<a name="getSettings"></a>
# **getSettings**
> GetSettings getSettings()

Returns settings

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.SettingsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

SettingsApi apiInstance = new SettingsApi();
try {
    GetSettings result = apiInstance.getSettings();
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling SettingsApi#getSettings");
    e.printStackTrace();
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetSettings**](GetSettings.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="getSettingsFor"></a>
# **getSettingsFor**
> GetSettingsFor getSettingsFor(collectionName)

Returns settings for collection

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.SettingsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

SettingsApi apiInstance = new SettingsApi();
Integer collectionName = 56; // Integer | Name of collection to return settings for
try {
    GetSettingsFor result = apiInstance.getSettingsFor(collectionName);
    System.out.println(result);
} catch (ApiException e) {
    System.err.println("Exception when calling SettingsApi#getSettingsFor");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **Integer**| Name of collection to return settings for |

### Return type

[**GetSettingsFor**](GetSettingsFor.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

<a name="updateSettings"></a>
# **updateSettings**
> updateSettings(collectionName, customData)

Update settings

### Example
```java
// Import classes:
//import io.directus.client.ApiClient;
//import io.directus.client.ApiException;
//import io.directus.client.Configuration;
//import io.directus.client.auth.*;
//import io.swagger.client.api.SettingsApi;

ApiClient defaultClient = Configuration.getDefaultApiClient();

// Configure API key authorization: api_key
ApiKeyAuth api_key = (ApiKeyAuth) defaultClient.getAuthentication("api_key");
api_key.setApiKey("YOUR API KEY");
// Uncomment the following line to set a prefix for the API key, e.g. "Token" (defaults to null)
//api_key.setApiKeyPrefix("Token");

SettingsApi apiInstance = new SettingsApi();
Integer collectionName = 56; // Integer | Name of collection to return settings for
String customData = "customData_example"; // String | Data based on your specific schema eg: active=1&title=LoremIpsum
try {
    apiInstance.updateSettings(collectionName, customData);
} catch (ApiException e) {
    System.err.println("Exception when calling SettingsApi#updateSettings");
    e.printStackTrace();
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **Integer**| Name of collection to return settings for |
 **customData** | **String**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum |

### Return type

null (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

