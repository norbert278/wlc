<section class="container mx-auto my-12 w-60 form">
    <h2 class="text-center text-2xl font-bold">{{ __('Submit your feedback', 'wlc') }}</h2>
    <form class="mt-6 flex flex-col gap-4" novalidate data-feedback-form>
        <input type="text" name="honeypot" class="hidden">
        <label class="flex flex-col gap-2">
            <span class="text-sm font-bold">{{ __('First name', 'wlc') }}</span>
            <input type="text" name="first_name" class="w-full rounded-md border p-2 text-sm border-black-300"
                   placeholder="{{ __('First name', 'wlc') }}" value="{!! $user_data['first_name'] !!}">
        </label>
        <label class="flex flex-col gap-2">
            <span class="text-sm font-bold">{{ __('Last name', 'wlc') }}</span>
            <input type="text" name="last_name" class="w-full rounded-md border p-2 text-sm border-black-300"
                   placeholder="{{ __('Last name', 'wlc') }}" value="{!! $user_data['last_name'] !!}">
        </label>
        <label class="flex flex-col gap-2">
            <span class="text-sm font-bold">{{ __('E-mail', 'wlc') }}</span>
            <input type="email" name="email" class="w-full rounded-md border p-2 text-sm border-black-300"
                   placeholder="{{ __('E-mail', 'wlc') }}" value="{!! $user_data['email'] !!}">
        </label>
        <label class="flex flex-col gap-2">
            <span class="text-sm font-bold">{{ __('Subject', 'wlc') }}</span>
            <input type="text" name="subject" class="w-full rounded-md border p-2 text-sm border-black-300"
                   placeholder="{{ __('Subject', 'wlc') }}">
        </label>
        <label class="flex flex-col gap-2">
            <span class="text-sm font-bold">{{ __('Message', 'wlc') }}</span>
            <textarea name="message" class="w-full rounded-md border p-2 text-sm border-black-300"
                      placeholder="{{ __('Message', 'wlc') }}"></textarea>
        </label>
        <input class="w-full rounded-md bg-black p-2 text-sm font-bold text-white cursor-pointer" type="submit"
               value="{{ __('Send', 'wlc') }}">
    </form>
</section>
