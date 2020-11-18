# IO.Directus.Api.SettingsApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetSettings**](SettingsApi.md#getsettings) | **GET** /settings | Returns settings
[**GetSettingsFor**](SettingsApi.md#getsettingsfor) | **GET** /settings/{collectionName} | Returns settings for collection
[**UpdateSettings**](SettingsApi.md#updatesettings) | **PUT** /settings/{collectionName} | Update settings


<a name="getsettings"></a>
# **GetSettings**
> GetSettings GetSettings ()

Returns settings

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetSettingsExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new SettingsApi();

            try
            {
                // Returns settings
                GetSettings result = apiInstance.GetSettings();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling SettingsApi.GetSettings: " + e.Message );
            }
        }
    }
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

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getsettingsfor"></a>
# **GetSettingsFor**
> GetSettingsFor GetSettingsFor (int? collectionName)

Returns settings for collection

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetSettingsForExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new SettingsApi();
            var collectionName = 56;  // int? | Name of collection to return settings for

            try
            {
                // Returns settings for collection
                GetSettingsFor result = apiInstance.GetSettingsFor(collectionName);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling SettingsApi.GetSettingsFor: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **int?**| Name of collection to return settings for | 

### Return type

[**GetSettingsFor**](GetSettingsFor.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="updatesettings"></a>
# **UpdateSettings**
> void UpdateSettings (int? collectionName, string customData)

Update settings

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class UpdateSettingsExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new SettingsApi();
            var collectionName = 56;  // int? | Name of collection to return settings for
            var customData = customData_example;  // string | Data based on your specific schema eg: active=1&title=LoremIpsum

            try
            {
                // Update settings
                apiInstance.UpdateSettings(collectionName, customData);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling SettingsApi.UpdateSettings: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **int?**| Name of collection to return settings for | 
 **customData** | **string**| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

