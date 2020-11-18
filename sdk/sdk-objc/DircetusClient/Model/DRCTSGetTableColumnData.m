#import "DRCTSGetTableColumnData.h"

@implementation DRCTSGetTableColumnData

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"columnName": @"column_name", @"type": @"type", @"charLength": @"char_length", @"isNullable": @"is_nullable", @"comment": @"comment", @"sort": @"sort", @"system": @"system", @"master": @"master", @"hiddenList": @"hidden_list", @"hiddenInput": @"hidden_input", @"required": @"required", @"columnType": @"column_type", @"isWritable": @"is_writable", @"ui": @"ui", @"options": @"options" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"columnName", @"type", @"charLength", @"isNullable", @"comment", @"sort", @"system", @"master", @"hiddenList", @"hiddenInput", @"required", @"columnType", @"isWritable", @"ui", @"options"];
  return [optionalProperties containsObject:propertyName];
}

@end
