#import "DRCTSTablesApi.h"
#import "DRCTSQueryParamCollection.h"
#import "DRCTSApiClient.h"
#import "DRCTSGetTable.h"
#import "DRCTSGetTableColumn.h"
#import "DRCTSGetTableColumns.h"
#import "DRCTSGetTableRow.h"
#import "DRCTSGetTableRows.h"
#import "DRCTSGetTables.h"


@interface DRCTSTablesApi ()

@property (nonatomic, strong, readwrite) NSMutableDictionary *mutableDefaultHeaders;

@end

@implementation DRCTSTablesApi

NSString* kDRCTSTablesApiErrorDomain = @"DRCTSTablesApiErrorDomain";
NSInteger kDRCTSTablesApiMissingParamErrorCode = 234513;

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
/// Create a column in a given table
/// 
///  @param tableId ID of table to return rows from 
///
///  @param tableName Name of table to add (optional)
///
///  @param columnName The unique name of the column to create (optional)
///
///  @param type The datatype of the column, eg: INT (optional)
///
///  @param ui The Directus Interface to use for this column (optional)
///
///  @param hiddenInput Whether the column will be hidden (globally) on the Edit Item page (optional)
///
///  @param hiddenList Whether the column will be hidden (globally) on the Item Listing page (optional)
///
///  @param required Whether the column is required. If required, the interface's validation function will be triggered (optional)
///
///  @param sort The sort order of the column used to override the column order in the schema (optional)
///
///  @param comment A helpful note to users for this column (optional)
///
///  @param relationshipType The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional)
///
///  @param relatedTable The table name this column is related to (only used when storing relational data) (optional)
///
///  @param junctionTable The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional)
///
///  @param junctionKeyLeft The column name in junction that is related to the column's table (only used when storing relational data) (optional)
///
///  @param junctionKeyRight The column name in junction that is related to the related table (only used when storing relational data) (optional)
///
///  @returns void
///
-(NSURLSessionTask*) addColumnWithTableId: (NSString*) tableId
    tableName: (NSString*) tableName
    columnName: (NSString*) columnName
    type: (NSString*) type
    ui: (NSString*) ui
    hiddenInput: (NSNumber*) hiddenInput
    hiddenList: (NSNumber*) hiddenList
    required: (NSNumber*) required
    sort: (NSNumber*) sort
    comment: (NSString*) comment
    relationshipType: (NSString*) relationshipType
    relatedTable: (NSString*) relatedTable
    junctionTable: (NSString*) junctionTable
    junctionKeyLeft: (NSString*) junctionKeyLeft
    junctionKeyRight: (NSString*) junctionKeyRight
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/columns"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json", @"application/xml"]];
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
    if (tableName) {
        formParams[@"table_name"] = tableName;
    }
    if (columnName) {
        formParams[@"column_name"] = columnName;
    }
    if (type) {
        formParams[@"type"] = type;
    }
    if (ui) {
        formParams[@"ui"] = ui;
    }
    if (hiddenInput) {
        formParams[@"hidden_input"] = hiddenInput;
    }
    if (hiddenList) {
        formParams[@"hidden_list"] = hiddenList;
    }
    if (required) {
        formParams[@"required"] = required;
    }
    if (sort) {
        formParams[@"sort"] = sort;
    }
    if (comment) {
        formParams[@"comment"] = comment;
    }
    if (relationshipType) {
        formParams[@"relationship_type"] = relationshipType;
    }
    if (relatedTable) {
        formParams[@"related_table"] = relatedTable;
    }
    if (junctionTable) {
        formParams[@"junction_table"] = junctionTable;
    }
    if (junctionKeyLeft) {
        formParams[@"junction_key_left"] = junctionKeyLeft;
    }
    if (junctionKeyRight) {
        formParams[@"junction_key_right"] = junctionKeyRight;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
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

///
/// Add a new row
/// 
///  @param tableId ID of table to return rows from 
///
///  @param customData Data based on your specific schema eg: active=1&title=LoremIpsum 
///
///  @returns void
///
-(NSURLSessionTask*) addRowWithTableId: (NSString*) tableId
    customData: (NSString*) customData
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'customData' is set
    if (customData == nil) {
        NSParameterAssert(customData);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"customData"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/rows"];

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
    bodyParam = customData;

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
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

///
/// Add a new table
/// 
///  @param name Name of table to add (optional)
///
///  @returns void
///
-(NSURLSessionTask*) addTableWithName: (NSString*) name
    completionHandler: (void (^)(NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json", @"application/xml"]];
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
    if (name) {
        formParams[@"name"] = name;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
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

///
/// Delete row
/// 
///  @param tableId ID of table to return rows from 
///
///  @param columnName Name of column to return 
///
///  @returns void
///
-(NSURLSessionTask*) deleteColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'columnName' is set
    if (columnName == nil) {
        NSParameterAssert(columnName);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"columnName"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/columns/{columnName}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (columnName != nil) {
        pathParams[@"columnName"] = columnName;
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

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"DELETE"
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

///
/// Delete row
/// 
///  @param tableId ID of table to return rows from 
///
///  @param rowId ID of row to return from rows 
///
///  @returns void
///
-(NSURLSessionTask*) deleteRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'rowId' is set
    if (rowId == nil) {
        NSParameterAssert(rowId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"rowId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/rows/{rowId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (rowId != nil) {
        pathParams[@"rowId"] = rowId;
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

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"DELETE"
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

///
/// Delete Table
/// 
///  @param tableId ID of table to return rows from 
///
///  @returns void
///
-(NSURLSessionTask*) deleteTableWithTableId: (NSString*) tableId
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}"];

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

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"DELETE"
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

///
/// Returns specific table
/// 
///  @param tableId ID of table to return rows from 
///
///  @returns DRCTSGetTable*
///
-(NSURLSessionTask*) getTableWithTableId: (NSString*) tableId
    completionHandler: (void (^)(DRCTSGetTable* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}"];

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
                              responseType: @"DRCTSGetTable*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTable*)data, error);
                                }
                            }];
}

///
/// Returns specific table column
/// 
///  @param tableId ID of table to return rows from 
///
///  @param columnName Name of column to return 
///
///  @returns DRCTSGetTableColumn*
///
-(NSURLSessionTask*) getTableColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
    completionHandler: (void (^)(DRCTSGetTableColumn* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    // verify the required parameter 'columnName' is set
    if (columnName == nil) {
        NSParameterAssert(columnName);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"columnName"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/columns/{columnName}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (columnName != nil) {
        pathParams[@"columnName"] = columnName;
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
                              responseType: @"DRCTSGetTableColumn*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTableColumn*)data, error);
                                }
                            }];
}

///
/// Returns table columns
/// 
///  @param tableId ID of table to return rows from 
///
///  @returns DRCTSGetTableColumns*
///
-(NSURLSessionTask*) getTableColumnsWithTableId: (NSString*) tableId
    completionHandler: (void (^)(DRCTSGetTableColumns* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/columns"];

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
                              responseType: @"DRCTSGetTableColumns*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTableColumns*)data, error);
                                }
                            }];
}

///
/// Returns specific table row
/// 
///  @param tableId ID of table to return rows from 
///
///  @param rowId ID of row to return from rows 
///
///  @returns DRCTSGetTableRow*
///
-(NSURLSessionTask*) getTableRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
    completionHandler: (void (^)(DRCTSGetTableRow* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    // verify the required parameter 'rowId' is set
    if (rowId == nil) {
        NSParameterAssert(rowId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"rowId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/rows/{rowId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (rowId != nil) {
        pathParams[@"rowId"] = rowId;
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
                              responseType: @"DRCTSGetTableRow*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTableRow*)data, error);
                                }
                            }];
}

///
/// Returns table rows
/// 
///  @param tableId ID of table to return rows from 
///
///  @returns DRCTSGetTableRows*
///
-(NSURLSessionTask*) getTableRowsWithTableId: (NSString*) tableId
    completionHandler: (void (^)(DRCTSGetTableRows* output, NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/rows"];

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
                              responseType: @"DRCTSGetTableRows*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTableRows*)data, error);
                                }
                            }];
}

///
/// Returns tables
/// 
///  @returns DRCTSGetTables*
///
-(NSURLSessionTask*) getTablesWithCompletionHandler: 
    (void (^)(DRCTSGetTables* output, NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

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
                              responseType: @"DRCTSGetTables*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetTables*)data, error);
                                }
                            }];
}

///
/// Update column
/// 
///  @param tableId ID of table to return rows from 
///
///  @param columnName Name of column to return 
///
///  @param dataType The datatype of the column, eg: INT (optional)
///
///  @param ui The Directus Interface to use for this column (optional)
///
///  @param hiddenInput Whether the column will be hidden (globally) on the Edit Item page (optional)
///
///  @param hiddenList Whether the column will be hidden (globally) on the Item Listing page (optional)
///
///  @param required Whether the column is required. If required, the interface's validation function will be triggered (optional)
///
///  @param sort The sort order of the column used to override the column order in the schema (optional)
///
///  @param comment A helpful note to users for this column (optional)
///
///  @param relationshipType The column's relationship type (only used when storing relational data) eg: ONETOMANY, MANYTOMANY or MANYTOONE (optional)
///
///  @param relatedTable The table name this column is related to (only used when storing relational data) (optional)
///
///  @param junctionTable The pivot/junction table that joins the column's table with the related table (only used when storing relational data) (optional)
///
///  @param junctionKeyLeft The column name in junction that is related to the column's table (only used when storing relational data) (optional)
///
///  @param junctionKeyRight The column name in junction that is related to the related table (only used when storing relational data) (optional)
///
///  @returns void
///
-(NSURLSessionTask*) updateColumnWithTableId: (NSString*) tableId
    columnName: (NSString*) columnName
    dataType: (NSString*) dataType
    ui: (NSString*) ui
    hiddenInput: (NSNumber*) hiddenInput
    hiddenList: (NSNumber*) hiddenList
    required: (NSNumber*) required
    sort: (NSNumber*) sort
    comment: (NSString*) comment
    relationshipType: (NSString*) relationshipType
    relatedTable: (NSString*) relatedTable
    junctionTable: (NSString*) junctionTable
    junctionKeyLeft: (NSString*) junctionKeyLeft
    junctionKeyRight: (NSString*) junctionKeyRight
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'columnName' is set
    if (columnName == nil) {
        NSParameterAssert(columnName);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"columnName"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/columns/{columnName}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (columnName != nil) {
        pathParams[@"columnName"] = columnName;
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
    if (dataType) {
        formParams[@"data_type"] = dataType;
    }
    if (ui) {
        formParams[@"ui"] = ui;
    }
    if (hiddenInput) {
        formParams[@"hidden_input"] = hiddenInput;
    }
    if (hiddenList) {
        formParams[@"hidden_list"] = hiddenList;
    }
    if (required) {
        formParams[@"required"] = required;
    }
    if (sort) {
        formParams[@"sort"] = sort;
    }
    if (comment) {
        formParams[@"comment"] = comment;
    }
    if (relationshipType) {
        formParams[@"relationship_type"] = relationshipType;
    }
    if (relatedTable) {
        formParams[@"related_table"] = relatedTable;
    }
    if (junctionTable) {
        formParams[@"junction_table"] = junctionTable;
    }
    if (junctionKeyLeft) {
        formParams[@"junction_key_left"] = junctionKeyLeft;
    }
    if (junctionKeyRight) {
        formParams[@"junction_key_right"] = junctionKeyRight;
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

///
/// Update row
/// 
///  @param tableId ID of table to return rows from 
///
///  @param rowId ID of row to return from rows 
///
///  @param customData Data based on your specific schema eg: active=1&title=LoremIpsum 
///
///  @returns void
///
-(NSURLSessionTask*) updateRowWithTableId: (NSString*) tableId
    rowId: (NSNumber*) rowId
    customData: (NSString*) customData
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'tableId' is set
    if (tableId == nil) {
        NSParameterAssert(tableId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'rowId' is set
    if (rowId == nil) {
        NSParameterAssert(rowId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"rowId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'customData' is set
    if (customData == nil) {
        NSParameterAssert(customData);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"customData"] };
            NSError* error = [NSError errorWithDomain:kDRCTSTablesApiErrorDomain code:kDRCTSTablesApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/tables/{tableId}/rows/{rowId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (tableId != nil) {
        pathParams[@"tableId"] = tableId;
    }
    if (rowId != nil) {
        pathParams[@"rowId"] = rowId;
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
    bodyParam = customData;

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
