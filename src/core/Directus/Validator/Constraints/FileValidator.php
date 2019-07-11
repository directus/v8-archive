<?php

namespace Directus\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FileValidator extends ConstraintValidator
{
    const KB_BYTES = 1000;
    const MB_BYTES = 1000000;
    const KIB_BYTES = 1024;
    const MIB_BYTES = 1048576;

    private static $suffices = [
        1 => 'bytes',
        self::KB_BYTES => 'kB',
        self::MB_BYTES => 'MB',
        self::KIB_BYTES => 'KiB',
        self::MIB_BYTES => 'MiB',
    ];

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if ($constraint->maxSize) {
            $limitInBytes = $constraint->maxSize;

            if ($sizeInBytes > $limitInBytes) {
                list($sizeAsString, $limitAsString, $suffix) = $this->factorizeSizes($sizeInBytes, $limitInBytes, $constraint->binaryFormat);
                $this->context->buildViolation($constraint->maxSizeMessage)
                    ->setParameter('{{ file }}', $this->formatValue($path))
                    ->setParameter('{{ size }}', $sizeAsString)
                    ->setParameter('{{ limit }}', $limitAsString)
                    ->setParameter('{{ suffix }}', $suffix)
                    ->setCode(File::TOO_LARGE_ERROR)
                    ->addViolation();

                return;
            }
        }
        if ($constraint->maxSize) {
            $sizeInBytes = $value;
            if (0 === $sizeInBytes) {
                $this->context->buildViolation($constraint->disallowEmptyMessage)
                    ->setParameter('{{ file }}', $this->formatValue($path))
                    ->setCode(File::EMPTY_ERROR)
                    ->addViolation();
    
                return;
            }
            $limitInBytes = $constraint->maxSize;

            if ($sizeInBytes > $limitInBytes) {
                list($sizeAsString, $limitAsString, $suffix) = $this->factorizeSizes($sizeInBytes, $limitInBytes, $constraint->binaryFormat);
                $this->context->buildViolation($constraint->maxSizeMessage)
                    ->setParameter('{{ file }}', $this->formatValue($path))
                    ->setParameter('{{ size }}', $sizeAsString)
                    ->setParameter('{{ limit }}', $limitAsString)
                    ->setParameter('{{ suffix }}', $suffix)
                    ->setCode(File::TOO_LARGE_ERROR)
                    ->addViolation();

                return;
            }
        }

        if ($constraint->mimeTypes) {

            $mimeTypes = (array) $constraint->mimeTypes;
            $mime = $value;

            foreach ($mimeTypes as $mimeType) {
                if ($mimeType === $mime) {
                    return;
                }

                if ($discrete = strstr($mimeType, '/*', true)) {
                    if (strstr($mime, '/', true) === $discrete) {
                        return;
                    }
                }
            }

            $this->context->buildViolation($constraint->mimeTypesMessage)
                ->setParameter('{{ file }}', $this->formatValue($path))
                ->setParameter('{{ type }}', $this->formatValue($mime))
                ->setParameter('{{ types }}', $this->formatValues($mimeTypes))
                ->setCode(File::INVALID_MIME_TYPE_ERROR)
                ->addViolation();
        }
    }

    private static function moreDecimalsThan($double, $numberOfDecimals)
    {
        return \strlen((string) $double) > \strlen(round($double, $numberOfDecimals));
    }

    /**
     * Convert the limit to the smallest possible number
     * (i.e. try "MB", then "kB", then "bytes").
     */
    private function factorizeSizes($size, $limit, $binaryFormat)
    {
        if ($binaryFormat) {
            $coef = self::MIB_BYTES;
            $coefFactor = self::KIB_BYTES;
        } else {
            $coef = self::MB_BYTES;
            $coefFactor = self::KB_BYTES;
        }

        $limitAsString = (string) ($limit / $coef);

        // Restrict the limit to 2 decimals (without rounding! we
        // need the precise value)
        while (self::moreDecimalsThan($limitAsString, 2)) {
            $coef /= $coefFactor;
            $limitAsString = (string) ($limit / $coef);
        }

        // Convert size to the same measure, but round to 2 decimals
        $sizeAsString = (string) round($size / $coef, 2);

        // If the size and limit produce the same string output
        // (due to rounding), reduce the coefficient
        while ($sizeAsString === $limitAsString) {
            $coef /= $coefFactor;
            $limitAsString = (string) ($limit / $coef);
            $sizeAsString = (string) round($size / $coef, 2);
        }

        return [$sizeAsString, $limitAsString, self::$suffices[$coef]];
    }
}
