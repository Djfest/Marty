<?php namespace Marty\NexGenRifle\Components;

use Cms\Classes\ComponentBase;
use Marty\NexGenRifle\Models\RifleBuild;
use Marty\NexGenRifle\Models\RifleItem;
use Marty\NexGenRifle\Models\ProductCatalog;
use Session;
use Auth;
use Input;
use Flash;
use ValidationException;

class BuilderUI extends ComponentBase
{
    protected $build;

    public function componentDetails()
    {
        return [
            'name'        => 'Rifle Builder Interface',
            'description' => 'Provides interface for creating custom rifle builds'
        ];
    }

    public function onRun()
    { 
        $this->addCss('plugins\marty\nexgenrifle\components\builderui\assets\css\builder.css');
        $this->addJs('assets/js/builder.js');
    }

    public function hasActiveBuild()
    {
        return $this->getActiveBuild() !== null;
    }

    public function getActiveBuild()
    {
        if ($this->build === null) {
            $buildId = Session::get('active_build_id');
            if ($buildId) {
                $this->build = RifleBuild::where('user_id', Auth::getUser()->id)
                    ->where('id', $buildId)
                    ->first();
            }
        }
        return $this->build;
    }

    public function getUserBuilds()
    {
        return RifleBuild::where('user_id', Auth::getUser()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRifleItem($category)
    {
        if ($build = $this->getActiveBuild()) {
            return RifleItem::where('rifle_build_id', $build->id)
                ->whereHas('product', function($query) use ($category) {
                    $query->whereHas('product_category', function($q) use ($category) {
                        $q->where('code', $category);
                    });
                })->first();
        }
        return null;
    }

    public function getProductsForCategory($category)
    {
        return ProductCatalog::whereHas('product_category', function($query) use ($category) {
            $query->where('code', $category);
        })->with(['vendor'])->get();
    }

    public function onCreateBuild()
    {
        $build = new RifleBuild;
        $build->user_id = Auth::getUser()->id;
        $build->title = 'New Build';
        $build->status = 'draft';
        $build->save();

        Session::put('active_build_id', $build->id);
        $this->build = $build;
    }

    public function onUpdateBuildTitle()
    {
        $build = $this->getActiveBuild();
        if (!$build) {
            throw new ValidationException(['title' => 'No active build found']);
        }

        $title = Input::get('title');
        if (!$title) {
            throw new ValidationException(['title' => 'Title cannot be empty']);
        }

        $build->title = $title;
        $build->save();

        return [
            '.build-info h2' => $title,
            '#notifications' => Flash::success('Build title updated successfully.')
        ];
    }

    public function onLoadBuild()
    {
        return [
            '#rifleBuilder' => $this->renderPartial('@_build_selector', [
                'builds' => $this->getUserBuilds()
            ])
        ];
    }

    public function onSelectBuild()
    {
        $buildId = post('build_id');
        if ($build = RifleBuild::find($buildId)) {
            if ($build->user_id == Auth::getUser()->id) {
                Session::put('active_build_id', $build->id);
                $this->build = $build;
            }
        }
    }

    public function onAddPart()
    {
        $build = $this->getActiveBuild();
        if (!$build) return;

        $productId = post('product_id');
        $category = post('category');

        // Remove existing part in this category if any
        RifleItem::where('rifle_build_id', $build->id)
            ->whereHas('product', function($query) use ($category) {
                $query->whereHas('product_category', function($q) use ($category) {
                    $q->where('code', $category);
                });
            })->delete();

        // Add new part
        $item = new RifleItem;
        $item->rifle_build_id = $build->id;
        $item->product_catalog_id = $productId;
        $item->save();

        $build->updateTotalCost();
    }

    public function onRemovePart()
    {
        $build = $this->getActiveBuild();
        if (!$build) return;

        $category = post('category');

        RifleItem::where('rifle_build_id', $build->id)
            ->whereHas('product', function($query) use ($category) {
                $query->whereHas('product_category', function($q) use ($category) {
                    $q->where('code', $category);
                });
            })->delete();

        $build->updateTotalCost();
    }

    public function onSaveBuild()
    {
        if ($build = $this->getActiveBuild()) {
            $build->updateTotalCost();
            $build->save();

            Flash::success('Build saved successfully.');
        }
    }

    public function onCancelBuild()
    {
        Session::forget('active_build_id');
        $this->build = null;
    }

    public function onFilterProducts()
    {
        $search = post('search');
        $category = post('category');

        $products = ProductCatalog::whereHas('product_category', function($query) use ($category) {
            $query->where('code', $category);
        });

        if ($search) {
            $products->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return [
            '#productsContainer' => $this->renderPartial('@_products_list', [
                'products' => $products->with(['vendor'])->get(),
                'category' => $category
            ])
        ];
    }

    protected function validateUser()
    {
        if (!Auth::check()) {
            throw new ApplicationException('You must be logged in to use the rifle builder.');
        }
    }
}
