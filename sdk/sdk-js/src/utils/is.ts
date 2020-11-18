/**
 * @module utils
 */

/**
 * @internal
 */
const isType = (t: string, v: any) => Object.prototype.toString.call(v) === `[object ${t}]`;
/**
 * @internal
 */
export const isNotNull = (v: any) => v !== null && v !== undefined;
/**
 * @internal
 */
export const isString = (v: any) => v && typeof v === "string" && /\S/.test(v);
/**
 * @internal
 */
export const isNumber = (v: any) => isType("Number", v) && isFinite(v) && !isNaN(parseFloat(v));
/**
 * @internal
 */
export const isFunction = (v: any) => v instanceof Function;
/**
 * @internal
 */
export const isObjectOrEmpty = (v: any) => isType("Object", v);
/**
 * @internal
 */
export const isArrayOrEmpty = (v: any) => isType("Array", v);
/**
 * @internal
 */
export const isArray = (v: any) => (!isArrayOrEmpty(v) ? false : v.length > 0);
/**
 * @internal
 */
export const isObject = (v: any) => {
  if (!isObjectOrEmpty(v)) {
    return false;
  }

  for (const key in v) {
    if (Object.prototype.hasOwnProperty.call(v, key)) {
      return true;
    }
  }

  return false;
};
