# Migration from IbrowsAssociationResolverBundle to IbrowsAttributeAssociationResolverBundle

## Requirements

- PHP 8.1+
- No `doctrine/annotations` dependency required

---

## Breaking Changes

### 1. Bundle registration

**Before:**
```php
// config/bundles.php
Ibrows\AssociationResolver\IbrowsAssociationResolverBundle::class => ['all' => true],
```

**After:**
```php
Ibrows\AttributeAssociationResolver\IbrowsAttributeAssociationResolverBundle::class => ['all' => true],
```

---

### 2. Annotation classes replaced by PHP Attributes

Any class used as an association annotation must now be a PHP Attribute. Constructor arguments replace public property injection.

**Before:**
```php
use Ibrows\AssociationResolver\Annotation\ManyToOne;

/**
 * @ManyToOne(targetFieldName="externalId", valueFieldName="externalId")
 */
private ?Country $country = null;
```

**After:**
```php
use Ibrows\AttributeAssociationResolver\Attribute\ManyToOne;

#[ManyToOne(targetFieldName: 'externalId', valueFieldName: 'externalId')]
private ?Country $country = null;
```

All four association types follow the same pattern:

| Old import | New import |
|-----------|-----------|
| `Ibrows\AssociationResolver\Annotation\ManyToOne` | `Ibrows\AttributeAssociationResolver\Attribute\ManyToOne` |
| `Ibrows\AssociationResolver\Annotation\OneToOne` | `Ibrows\AttributeAssociationResolver\Attribute\OneToOne` |
| `Ibrows\AssociationResolver\Annotation\ManyToMany` | `Ibrows\AttributeAssociationResolver\Attribute\ManyToMany` |
| `Ibrows\AssociationResolver\Annotation\OneToMany` | `Ibrows\AttributeAssociationResolver\Attribute\OneToMany` |

---

### 3. Constructor arguments replace public property assignment

Annotation classes previously relied on Doctrine setting public properties directly. The attribute classes use named constructor arguments instead.

All parameters are optional and match the old property names exactly:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `targetFieldName` | `string` | `''` | Field on the target entity to match against |
| `valueFieldName` | `string` | `''` | Field on the source entity whose value is used for lookup |
| `entitySetterName` | `?string` | `null` | Override the setter method name (default: `set{PropertyName}`) |
| `entityGetterName` | `?string` | `null` | Override the getter method name (default: `get{PropertyName}`) |
| `valueGetterName` | `?string` | `null` | Override the value getter method name |
| `targetEntity` | `?string` | `null` | Override the target entity class (defaults to Doctrine mapping) |

`OneToMany` additionally accepts:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `collectionAddFunctionName` | `?string` | `null` | Override the collection add method (default: `add{PropertyName}`) |
| `collectionRemoveFunctionName` | `?string` | `null` | Override the collection remove method (default: `remove{PropertyName}`) |

---

### 4. No Doctrine AnnotationReader setup required

The old bundle required wiring a Doctrine `AnnotationReader` into the service. The new bundle uses PHP's native Reflection API and needs no such setup.

**Before** (manual wiring, if done outside the bundle):
```php
$reader = new \Ibrows\AnnotationReader\AnnotationReader();
$reader->setAnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());
```

**After:**
```php
$reader = new \Ibrows\AttributeReader\AttributeReader();
// No further setup needed.
```

---

### 5. Service ID prefix changed

All service IDs use the new prefix `ibrows_attributeassociationresolver` instead of `ibrows_associationresolver`.

| Old service ID | New service ID |
|---------------|---------------|
| `ibrows_associationresolver.resolver` | `ibrows_attributeassociationresolver.resolver` |
| `ibrows_associationresolver.resolverchain` | `ibrows_attributeassociationresolver.resolverchain` |
| `ibrows_associationresolver.annotation.reader` | `ibrows_attributeassociationresolver.attribute.reader` |
| `ibrows_associationresolver.doctrine_annotations.reader` | *(removed — no longer needed)* |
| `ibrows_associationresolver.resolver.manytomany` | `ibrows_attributeassociationresolver.resolver.manytomany` |
| `ibrows_associationresolver.resolver.manytoone` | `ibrows_attributeassociationresolver.resolver.manytoone` |
| `ibrows_associationresolver.resolver.onetoone` | `ibrows_attributeassociationresolver.resolver.onetoone` |
| `ibrows_associationresolver.resolver.onetomany` | `ibrows_attributeassociationresolver.resolver.onetomany` |

---

### 6. Parameter names changed

| Old parameter | New parameter |
|--------------|--------------|
| `ibrows_associationresolver.softdelete` | `ibrows_attributeassociationresolver.softdelete` |
| `ibrows_associationresolver.softdeletegetter` | `ibrows_attributeassociationresolver.softdeletegetter` |

---

### 7. Namespace changes

| Old namespace | New namespace |
|--------------|--------------|
| `Ibrows\AssociationResolver` | `Ibrows\AttributeAssociationResolver` |
| `Ibrows\AnnotationReader` | `Ibrows\AttributeReader` |
| `Ibrows\AssociationResolver\Annotation\*` | `Ibrows\AttributeAssociationResolver\Attribute\*` |
| `Ibrows\AssociationResolver\Reader\AssociationAnnotationReader` | `Ibrows\AttributeAssociationResolver\Reader\AssociationAttributeReader` |
| `Ibrows\AssociationResolver\Reader\AssociationAnnotationReaderInterface` | `Ibrows\AttributeAssociationResolver\Reader\AssociationAttributeReaderInterface` |

All other classes (`Resolver`, `ResolverChain`, `ResultBag`, `AssociationMappingInfo`, exceptions, etc.) keep their names — only the root namespace changes.

---

## What Stays the Same

- The resolver logic (`Resolver`, `ResolverChain`, resolver types) is unchanged
- `AssociationMappingInfo` and `AssociationMappingInfoInterface` are unchanged
- `ResultBag` is unchanged
- The `getType()` matching strategy between annotations and resolver types is unchanged
- Soft-delete support (`setSoftdeletable`, `setSoftdeletableGetter`) is unchanged
- `diffMode` and `QueryBuilder` support in `resolveAssociations()` is unchanged
- The tag name for custom resolvers follows the same pattern: `ibrows_attributeassociationresolver.resolverchain`
