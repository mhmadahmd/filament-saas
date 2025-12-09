<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mhmadahmd\FilamentSaas\Interval;
use Mhmadahmd\FilamentSaas\Models\Feature;
use Mhmadahmd\FilamentSaas\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => ['en' => 'Free', 'ar' => 'مجاني'],
                'description' => [
                    'en' => 'Perfect for getting started with basic features',
                    'ar' => 'مثالي للبدء بالميزات الأساسية',
                ],
                'slug' => 'free',
                'is_active' => true,
                'price' => 0.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'trial_period' => 0,
                'trial_interval' => Interval::DAY->value,
                'invoice_period' => 1,
                'invoice_interval' => Interval::MONTH->value,
                'grace_period' => 0,
                'grace_interval' => Interval::DAY->value,
                'active_subscribers_limit' => null,
                'sort_order' => 1,
                'features' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'description' => ['en' => '1 GB of storage space', 'ar' => '1 جيجابايت من مساحة التخزين'],
                        'slug' => 'storage',
                        'value' => '1073741824',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => ['en' => 'Users', 'ar' => 'المستخدمون'],
                        'description' => ['en' => 'Up to 1 user', 'ar' => 'حتى مستخدم واحد'],
                        'slug' => 'users',
                        'value' => '1',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => ['en' => 'API Calls', 'ar' => 'استدعاءات API'],
                        'description' => ['en' => '1,000 API calls per month', 'ar' => '1,000 استدعاء API شهرياً'],
                        'slug' => 'api-calls',
                        'value' => '1000',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 3,
                    ],
                    [
                        'name' => ['en' => 'Support', 'ar' => 'الدعم'],
                        'description' => ['en' => 'Community support', 'ar' => 'دعم المجتمع'],
                        'slug' => 'support',
                        'value' => 'community',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Starter', 'ar' => 'البداية'],
                'description' => [
                    'en' => 'Ideal for small teams and growing businesses',
                    'ar' => 'مثالي للفرق الصغيرة والشركات الناشئة',
                ],
                'slug' => 'starter',
                'is_active' => true,
                'price' => 29.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'trial_period' => 14,
                'trial_interval' => Interval::DAY->value,
                'invoice_period' => 1,
                'invoice_interval' => Interval::MONTH->value,
                'grace_period' => 7,
                'grace_interval' => Interval::DAY->value,
                'active_subscribers_limit' => null,
                'sort_order' => 2,
                'features' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'description' => ['en' => '10 GB of storage space', 'ar' => '10 جيجابايت من مساحة التخزين'],
                        'slug' => 'storage',
                        'value' => '10737418240',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => ['en' => 'Users', 'ar' => 'المستخدمون'],
                        'description' => ['en' => 'Up to 5 users', 'ar' => 'حتى 5 مستخدمين'],
                        'slug' => 'users',
                        'value' => '5',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => ['en' => 'API Calls', 'ar' => 'استدعاءات API'],
                        'description' => ['en' => '50,000 API calls per month', 'ar' => '50,000 استدعاء API شهرياً'],
                        'slug' => 'api-calls',
                        'value' => '50000',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 3,
                    ],
                    [
                        'name' => ['en' => 'Support', 'ar' => 'الدعم'],
                        'description' => ['en' => 'Email support with 48-hour response time', 'ar' => 'دعم عبر البريد الإلكتروني مع وقت استجابة 48 ساعة'],
                        'slug' => 'support',
                        'value' => 'email',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 4,
                    ],
                    [
                        'name' => ['en' => 'Advanced Analytics', 'ar' => 'التحليلات المتقدمة'],
                        'description' => ['en' => 'Access to advanced analytics dashboard', 'ar' => 'الوصول إلى لوحة تحليلات متقدمة'],
                        'slug' => 'advanced-analytics',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Professional', 'ar' => 'المهني'],
                'description' => [
                    'en' => 'For established businesses with advanced needs',
                    'ar' => 'للشركات الراسخة ذات الاحتياجات المتقدمة',
                ],
                'slug' => 'professional',
                'is_active' => true,
                'price' => 99.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'trial_period' => 30,
                'trial_interval' => Interval::DAY->value,
                'invoice_period' => 1,
                'invoice_interval' => Interval::MONTH->value,
                'grace_period' => 14,
                'grace_interval' => Interval::DAY->value,
                'active_subscribers_limit' => null,
                'sort_order' => 3,
                'features' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'description' => ['en' => '100 GB of storage space', 'ar' => '100 جيجابايت من مساحة التخزين'],
                        'slug' => 'storage',
                        'value' => '107374182400',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => ['en' => 'Users', 'ar' => 'المستخدمون'],
                        'description' => ['en' => 'Up to 25 users', 'ar' => 'حتى 25 مستخدم'],
                        'slug' => 'users',
                        'value' => '25',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => ['en' => 'API Calls', 'ar' => 'استدعاءات API'],
                        'description' => ['en' => '500,000 API calls per month', 'ar' => '500,000 استدعاء API شهرياً'],
                        'slug' => 'api-calls',
                        'value' => '500000',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 3,
                    ],
                    [
                        'name' => ['en' => 'Support', 'ar' => 'الدعم'],
                        'description' => ['en' => 'Priority email support with 24-hour response time', 'ar' => 'دعم بريد إلكتروني ذو أولوية مع وقت استجابة 24 ساعة'],
                        'slug' => 'support',
                        'value' => 'priority-email',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 4,
                    ],
                    [
                        'name' => ['en' => 'Advanced Analytics', 'ar' => 'التحليلات المتقدمة'],
                        'description' => ['en' => 'Access to advanced analytics dashboard', 'ar' => 'الوصول إلى لوحة تحليلات متقدمة'],
                        'slug' => 'advanced-analytics',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 5,
                    ],
                    [
                        'name' => ['en' => 'Custom Integrations', 'ar' => 'التكاملات المخصصة'],
                        'description' => ['en' => 'Access to custom API integrations', 'ar' => 'الوصول إلى تكاملات API مخصصة'],
                        'slug' => 'custom-integrations',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 6,
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Enterprise', 'ar' => 'المؤسسات'],
                'description' => [
                    'en' => 'For large organizations with unlimited needs',
                    'ar' => 'للمؤسسات الكبيرة ذات الاحتياجات غير المحدودة',
                ],
                'slug' => 'enterprise',
                'is_active' => true,
                'price' => 299.00,
                'signup_fee' => 0.00,
                'currency' => 'USD',
                'trial_period' => 30,
                'trial_interval' => Interval::DAY->value,
                'invoice_period' => 1,
                'invoice_interval' => Interval::MONTH->value,
                'grace_period' => 30,
                'grace_interval' => Interval::DAY->value,
                'active_subscribers_limit' => null,
                'sort_order' => 4,
                'features' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'description' => ['en' => 'Unlimited storage space', 'ar' => 'مساحة تخزين غير محدودة'],
                        'slug' => 'storage',
                        'value' => 'unlimited',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => ['en' => 'Users', 'ar' => 'المستخدمون'],
                        'description' => ['en' => 'Unlimited users', 'ar' => 'مستخدمون غير محدودين'],
                        'slug' => 'users',
                        'value' => 'unlimited',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => ['en' => 'API Calls', 'ar' => 'استدعاءات API'],
                        'description' => ['en' => 'Unlimited API calls per month', 'ar' => 'استدعاءات API غير محدودة شهرياً'],
                        'slug' => 'api-calls',
                        'value' => 'unlimited',
                        'resettable_period' => 1,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 3,
                    ],
                    [
                        'name' => ['en' => 'Support', 'ar' => 'الدعم'],
                        'description' => ['en' => '24/7 dedicated support with 1-hour response time', 'ar' => 'دعم مخصص على مدار الساعة مع وقت استجابة ساعة واحدة'],
                        'slug' => 'support',
                        'value' => 'dedicated',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 4,
                    ],
                    [
                        'name' => ['en' => 'Advanced Analytics', 'ar' => 'التحليلات المتقدمة'],
                        'description' => ['en' => 'Access to advanced analytics dashboard with custom reports', 'ar' => 'الوصول إلى لوحة تحليلات متقدمة مع تقارير مخصصة'],
                        'slug' => 'advanced-analytics',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 5,
                    ],
                    [
                        'name' => ['en' => 'Custom Integrations', 'ar' => 'التكاملات المخصصة'],
                        'description' => ['en' => 'Access to custom API integrations and webhooks', 'ar' => 'الوصول إلى تكاملات API مخصصة وwebhooks'],
                        'slug' => 'custom-integrations',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 6,
                    ],
                    [
                        'name' => ['en' => 'Dedicated Account Manager', 'ar' => 'مدير حساب مخصص'],
                        'description' => ['en' => 'Dedicated account manager for personalized support', 'ar' => 'مدير حساب مخصص للدعم الشخصي'],
                        'slug' => 'account-manager',
                        'value' => 'true',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 7,
                    ],
                    [
                        'name' => ['en' => 'SLA Guarantee', 'ar' => 'ضمان SLA'],
                        'description' => ['en' => '99.9% uptime SLA guarantee', 'ar' => 'ضمان SLA بنسبة 99.9%'],
                        'slug' => 'sla-guarantee',
                        'value' => '99.9',
                        'resettable_period' => 0,
                        'resettable_interval' => Interval::MONTH->value,
                        'sort_order' => 8,
                    ],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::create($planData);

            foreach ($features as $featureData) {
                Feature::create(array_merge($featureData, ['plan_id' => $plan->id]));
            }
        }
    }
}

