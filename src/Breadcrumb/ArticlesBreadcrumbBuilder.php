<?php
namespace Drupal\wm_breadcrumbs\Breadcrumb;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * @file
 * Contains \Drupal\wm_breadcrumb\Breadcrumb\ArticlesBreadcrumbBuilder.
 */
class ArticlesBreadcrumbBuilder implements BreadcrumbBuilderInterface {
    use StringTranslationTrait;

    /**
     * {@inheritdoc}
     */
    public function applies(RouteMatchInterface $route_match) { //область применения
        // Get node from current route
        $node = $route_match->getParameter('node');
        if ($node instanceof NodeInterface) {
            // If change object node, get node type(bundle).
            $node_type = $node->getType();
        }
        // If node type is article, Return TRUE.
        return (isset($node_type) && $node_type == 'product');
    }

    /**
     * {@inheritdoc}
     */
    public function build(RouteMatchInterface $route_match) {
        // Create new Breadcrumb.
        $breadcrumb = new Breadcrumb();
        $breadcrumb->addCacheContexts(['url.path']);
        // Add front page link usin addlink method.
        $breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
        //Get node value parametr.
        $node = $route_match->getParameter('node');
        // Get field_category value.
        /** @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $category_field */
        $category_field = $node->field_category;
        $category = $category_field->referencedEntities();
        if (!empty($category)) {
            // Get first array value
            $category = array_reverse($category);
            // Add taxonony term received link.
            $breadcrumb->addLink(Link::createFromRoute($category->label(), 'entity.taxonomy_term.canonical', ['taxonomy_term' => $category->id()]));
        }
        return $breadcrumb;
    }
}

