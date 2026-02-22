<?php
namespace App\Serializer;

use App\Model\AttachmentInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class AttachmentNormalizer implements NormalizerInterface, NormalizerAwareInterface {
    use NormalizerAwareTrait;

    private const string ALREADY_CALLED = 'attachment_normalizer_already_called';

    public function __construct(
        private readonly StorageInterface $storage
    )
    {
    }

    /**
     * @param AttachmentInterface $object
     */
    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;
        $object->setContentUrl($this->storage->resolveUri($object, 'file'));

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return $data instanceof AttachmentInterface;
    }
    
    public function getSupportedTypes(?string $format = null) : array
    {
        // depends on context flag; not cacheable
        return [AttachmentInterface::class => false];
    }
}
