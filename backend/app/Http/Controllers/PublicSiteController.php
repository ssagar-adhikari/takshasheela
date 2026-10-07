<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\Accommodation;
use App\Models\ChronicleArticle;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\WellnessCategory;
use App\Services\EnquiryMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        $homeSections = HomeSection::whereIn('slug', ['hero', 'philosophy'])->get()->keyBy('slug');
        $aboutContent = AboutSection::where('slug', 'about-takshasheela')->value('content');
        if (! $aboutContent) {
            $aboutDefaults = json_decode(file_get_contents(database_path('seeders/data/about-sections.json')), true, 512, JSON_THROW_ON_ERROR);
            $aboutContent = $aboutDefaults['about-takshasheela'];
        }

        return view('site.home', [
            'hero' => array_replace(config('home.sections.hero'), $homeSections->get('hero')?->content ?? []),
            'philosophy' => array_replace(config('home.sections.philosophy'), $homeSections->get('philosophy')?->content ?? []),
            'aboutIntro' => $aboutContent['blocks']['block-1'],
            'packages' => WellnessCategory::published()->where('slug', 'programs')->first()?->offerings()->published()->limit(3)->get() ?? collect(),
            'products' => Product::published()->orderBy('sort_order')->orderBy('name')->limit(3)->get(),
            'testimonials' => Testimonial::published()->orderBy('sort_order')->orderBy('guest_name')->limit(3)->get(),
            'blogs' => $this->articles('blog')->take(3),
            'news' => $this->articles('news')->take(3),
        ]);
    }

    public function about(string $section): View
    {
        abort_unless(array_key_exists($section, config('about.sections')), 404);
        $content = AboutSection::where('slug', $section)->first()?->content;

        if (! $content) {
            $defaults = json_decode(file_get_contents(database_path('seeders/data/about-sections.json')), true, 512, JSON_THROW_ON_ERROR);
            $content = $defaults[$section];
        }

        return view('site.about', compact('content', 'section'));
    }

    public function accommodations(): View
    {
        return view('site.accommodations.index', [
            'accommodations' => Accommodation::published()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function accommodation(string $slug): View
    {
        $accommodation = Accommodation::published()->where('slug', $slug)->firstOrFail();

        return view('site.accommodations.show', [
            'accommodation' => $accommodation,
            'related' => Accommodation::published()->whereKeyNot($accommodation->id)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function products(): View
    {
        return view('site.products.index', [
            'products' => Product::published()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function product(string $slug): View
    {
        $product = Product::published()->where('slug', $slug)->firstOrFail();

        return view('site.products.show', [
            'product' => $product,
            'related' => Product::published()->whereKeyNot($product->id)->orderBy('sort_order')->orderBy('name')->limit(3)->get(),
        ]);
    }

    public function blogs(): View
    {
        return $this->chronicleListing('blog');
    }

    public function news(): View
    {
        return $this->chronicleListing('news');
    }

    public function chronicle(string $slug): View
    {
        return view('site.chronicles.show', [
            'article' => ChronicleArticle::published()->where('slug', $slug)->firstOrFail(),
        ]);
    }

    public function testimonials(): View
    {
        return view('site.testimonials', [
            'testimonials' => Testimonial::published()->orderBy('sort_order')->orderBy('guest_name')->get(),
        ]);
    }

    public function gallery(): View
    {
        return view('site.gallery', [
            'images' => GalleryItem::published()->orderBy('sort_order')->get(),
        ]);
    }

    public function contact(Request $request): View
    {
        [$typeLabels, $interestGroups, $interestToType] = $this->contactOptions();
        $interest = trim((string) $request->query('interest'));
        $type = trim((string) $request->query('type'));
        $type = ['wellness_package' => 'wellness:programs', 'ayurvedic_service' => 'wellness:therapies', 'wellness_training' => 'wellness:trainings'][$type] ?? $type;

        if (isset($interestToType[$interest])) {
            $type = $interestToType[$interest];
        }
        if (! isset($typeLabels[$type])) {
            $type = '';
        }

        return view('site.contact', compact('typeLabels', 'interestGroups', 'type', 'interest'));
    }

    public function submitContact(Request $request, EnquiryMailService $mailService): RedirectResponse
    {
        [$typeLabels, , $interestToType] = $this->contactOptions();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'enquiry_type' => ['required', Rule::in(array_keys($typeLabels))],
            'interest' => ['nullable', 'string', Rule::in(array_keys($interestToType))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ]);

        if (($data['interest'] ?? '') !== '' && $interestToType[$data['interest']] !== $data['enquiry_type']) {
            return back()->withInput()->withErrors(['interest' => 'The selected item does not match the enquiry type.']);
        }

        $enquiry = Enquiry::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'enquiry_type' => $data['enquiry_type'],
            'enquiry_type_label' => $typeLabels[$data['enquiry_type']],
            'interest' => $data['interest'] ?? null,
            'message' => $data['message'],
            'consent' => true,
        ]);

        $mailService->send($enquiry);

        return redirect()->route('contact')->with('status', 'Thank you. Your enquiry has been received. Our team will respond shortly.');
    }

    public function privacy(): View
    {
        return view('site.privacy');
    }

    public function legacyDetail(Request $request, string $resource): RedirectResponse
    {
        $routes = [
            'programs' => ['programs.index', 'programs.show'],
            'therapies' => ['therapies.index', 'therapies.show'],
            'trainings' => ['trainings.index', 'trainings.show'],
            'accommodations' => ['accommodations.index', 'accommodations.show'],
            'products' => ['products.index', 'products.show'],
            'chronicles' => ['blogs.index', 'chronicles.show'],
        ];
        abort_unless(isset($routes[$resource]), 404);

        $slug = trim((string) $request->query('slug'));

        return $slug === ''
            ? redirect()->route($routes[$resource][0])
            : redirect()->route($routes[$resource][1], $slug);
    }

    private function chronicleListing(string $type): View
    {
        $articles = $this->articles($type);
        $featured = $articles->firstWhere('is_featured', true) ?? $articles->first();

        return view('site.chronicles.index', compact('articles', 'featured', 'type'));
    }

    private function articles(string $type)
    {
        return ChronicleArticle::published()->where('type', $type)->orderBy('sort_order')->orderBy('title')->get();
    }

    private function contactOptions(): array
    {
        $typeLabels = [
            'accommodation' => 'Accommodation',
            'product' => 'Product',
            'general' => 'General enquiry',
        ];
        $interestGroups = [
            'accommodation' => ['label' => 'Accommodations', 'options' => Accommodation::published()->orderBy('sort_order')->pluck('name')->all()],
            'product' => ['label' => 'Products', 'options' => Product::published()->orderBy('sort_order')->pluck('name')->all()],
        ];
        $categories = WellnessCategory::published()->with(['subcategories' => fn ($query) => $query->published()->with(['offerings' => fn ($query) => $query->published()])])->orderBy('sort_order')->get();
        foreach ($categories as $category) {
            $type = 'wellness:'.$category->slug;
            $typeLabels[$type] = $category->name;
            $interestGroups[$type] = ['label' => $category->name, 'options' => $category->subcategories->flatMap->offerings->pluck('name')->all()];
        }

        $interestToType = [];
        foreach ($interestGroups as $type => $group) {
            foreach ($group['options'] as $option) {
                $interestToType[$option] = $type;
            }
        }

        return [$typeLabels, $interestGroups, $interestToType];
    }
}
