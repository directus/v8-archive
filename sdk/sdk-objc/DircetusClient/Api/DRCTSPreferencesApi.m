#import "DRCTSPreferencesApi.h"
#import "DRCTSQueryParamCollection.h"
#import "DRCTSApiClient.h"
#import "DRCTSGetPreferences.h"


@interface DRCTSPreferencesApi ()

@property (nonatomic, strong, readwrite) NSMutableDictionary *mutableDefaultHeaders;

@end

@implementation DRCTSPreferencesApi

NSString* kDRCTSPreferencesApiErrorDomain = @"DRCTSPreferencesApiErrorDomain";
NSInteger kDRCTSPreferencesApiMissingParamErrorCode = 234513;

@synthesize apiClient = _apiClient;

#pragma mark - Initialize methods

- (instancetype) init {
    return [self initWithApiClient:[DRCTSApiClient sharedClient]];
}


-(instancetype) initWithApiClient:(DRCTSApiClient *)apiClient {
    self = [super init];
    if (self) {
        _apiClient = apiClient;
        _mutableDefaultHeaders = [NSMutableDictionary dictionary];
    }
    return self;
}

#pragma mark -

-(NSString*) defaultHeaderForKey:(NSString*)key {
    return self.mutableDefaultHeaders[key];
}

-(void) setDefaultHeaderValue:(NSString*) value forKey:(NSString*)key {
    [self.mutableDefaultHeaders setValue:value forKey:key];
}

-(NSDictionary *)defaultHeaders {
    return self.mutableDefaultHeaders;
}

#pragma mark - Api Methods

///
/// Returns table preferences
/// 
///  @param tableId ID of table to return rows from 
///
///  @returns DRCTSGetPreferences*
///
-(NSURLSessionTask*) getPreferencesWithTableId: (NSString*) tableId
    completionHandler: (void (^)(DRCTSGetPreferences* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSPreferencesApiErrorDomain code:kDRCTSPreferencesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/preferences"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetPreferences*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetPreferences*)data, error);
                                }
                            }];
}

///
/// Update table preferences
/// 
///  @param tableId ID of table to return rows from 
///
///  @param _id Preference's Unique Identification number (optional)
///
///  @param tableName Name of table to add (optional)
///
///  @param columnsVisible List of visible columns, separated by commas (optional)
///
///  @param sort The sort order of the column used to override the column order in the schema (optional)
///
///  @param sortOrder Sort Order (ASC=Ascending or DESC=Descending) (optional)
///
///  @param status List of status values. separated by comma (optional)
///
///  @returns void
///
-(NSURLSessionTask*) updatePreferencesWithTableId: (NSString*) tableId
    _id: (NSString*) _id
    tableName: (NSString*) tableName
    columnsVisible: (NSString*) columnsVisible
    sort: (NSNumber*) sort
    sortOrder: (NSString*) sortOrder
    status: (NSString*) status
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSPreferencesApiErrorDomain code:kDRCTSPreferencesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/preferences"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[@"application/x-www-form-urlencoded"]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];
    if (_id) {
        formParams[@"id"] = _id;
    }
    if (tableName) {
        formParams[@"table_name"] = tableName;
    }
    if (columnsVisible) {
        formParams[@"columns_visible"] = columnsVisible;
    }
    if (sort) {
        formParams[@"sort"] = sort;
    }
    if (sortOrder) {
        formParams[@"sort_order"] = sortOrder;
    }
    if (status) {
        formParams[@"status"] = status;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"PUT"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}



@end
