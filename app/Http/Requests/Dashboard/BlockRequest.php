<?php

namespace App\Http\Requests\Dashboard;

use App\Models\AreaResponsible;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // تعريف قواعد التحقق من الصحة الأساسية
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'aid_id' => ['nullable', 'exists:area_responsibles,aid_id'], // nullable لأن العمود في DB nullable
            'area_responsible_id' => ['nullable', 'exists:users,id'],
            'phone' => ['required', 'string', 'regex:/^(059|056)\d{7}$/'], // int(11) في DB
            // 'limit_num' => ['required', 'integer'], // int(11) في DB
            'lan' => ['required', 'string', 'max:200'],
            'lat' => ['required', 'string', 'max:200'],
            'note' => ['required', 'string'], // text في DB
        ];

        // قاعدة التحقق من 'title' تختلف بين الإنشاء والتحديث
        if ($this->isMethod('POST')) { // عند إنشاء سجل جديد (Store)
            $rules['title'] = ['required', 'string', 'max:255', 'unique:blocks,title'];
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) { // عند تحديث سجل موجود (Update)
            // عند التحديث، يجب أن نتجاهل الـ title الخاص بالسجل الحالي نفسه
            // $this->block هو الموديل الذي يتم تمريره للـ Form Request في حالة التحديث
            $rules['title'] = ['required', 'string', 'max:255', Rule::unique('blocks', 'title')->ignore($this->block)];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('blocks.attributes');
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $data = $this->all();

        // المنطق الجديد: إذا تم توفير aid_id في الطلب، استخدمه لإيجاد area_responsible_id
        if (isset($data['aid_id'])) {
            // البحث عن مسؤول المنطقة بناءً على aid_id
            // تأكد أن AreaResponsible::where('aid_id', ...) يشير إلى العمود الصحيح في جدول area_responsibles
            // وأن AreaResponsible->id هو المفتاح الأساسي (id) الذي تريد تخزينه في blocks.area_responsible_id
            $areaResponsible = AreaResponsible::where('aid_id', $data['aid_id'])->first();

            if ($areaResponsible) {
                // إذا تم العثور على مسؤول المنطقة، قم بدمج الـ ID الخاص به في area_responsible_id
                $this->merge([
                    'area_responsible_id' => $areaResponsible->id,
                ]);
            } else {
                // إذا تم توفير aid_id ولكن لم يتم العثور على مسؤول المنطقة المقابل له،
                // قم بتعيين area_responsible_id إلى NULL لتجنب فشل المفتاح الأجنبي مع جدول users.
                // يمكنك أيضاً إضافة خطأ تحقق مخصص هنا إذا كان aid_id يجب أن يكون صالحاً دائماً.
                $this->merge([
                    'area_responsible_id' => null,
                ]);
                // مثال لإضافة خطأ تحقق مخصص:
                // $this->validator->errors()->add('aid_id', 'معرف مسؤول المنطقة المقدم غير صالح.');
            }
        } else {
            // إذا لم يتم توفير aid_id في الطلب، واستخدم منطق المشرف إذا كان موجوداً
            // (هذا المنطق سيعمل فقط إذا لم يتم تعيين area_responsible_id من aid_id)
            if (auth()->user()?->isSupervisor()) {
                $this->merge([
                    'area_responsible_id' => auth()->id(),
                ]);
            }
        }
    }
}
