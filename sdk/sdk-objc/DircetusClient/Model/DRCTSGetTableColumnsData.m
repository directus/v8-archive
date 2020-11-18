#import "DRCTSGetTableColumnsData.h"

@implementation DRCTSGetTableColumnsData

- (instancetype)init {
  self = [super init];
  if (self) {
    // initialize property's default value, if any
    
  }
  return self;
}


/**
 * Maps json key to property name.
 * This method is used by `JSONModel`.
 */
+ (JSONKeyMapper *)keyMapper {
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"name": @"name", @"_id": @"id", @"columnName": @"column_name", @"type": @"type", @"length": @"length", @"precision": @"precision", @"scale": @"scale", @"sort": @"sort", @"defaultValue": @"default_value", @"nullable": @"nullable", @"key": @"key", @"extraOptions": @"extra_options", @"options": @"options", @"tableName": @"table_name", @"required": @"required", @"ui": @"ui", @"hiddenList": @"hidden_list", @"hiddenInput": @"hidden_input", @"relationship": @"relationship", @"comment": @"comment" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"name", @"_id", @"columnName", @"type", @"length", @"precision", @"scale", @"sort", @"defaultValue", @"nullable", @"key", @"extraOptions", @"options", @"tableName", @"required", @"ui", @"hiddenList", @"hiddenInput", @"relationship", @"comment"];
  return [optionalProperties containsObject:propertyName];
}

@end
