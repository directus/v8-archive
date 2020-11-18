/**
 * @module utils
 */

/**
 * Checks invariant violation against a condition, will throw an error if not fulfilled
 * @internal
 * @param {boolean} condition
 * @param {string} message
 */
export const invariant = (condition: boolean | null | undefined, message: string): void => {
  if (!!condition === true) {
    return;
  }

  throw new Error(`Invariant violation: ${message}`);
};
