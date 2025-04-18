@component('mail::message')
# Olá, {{ $user }}! 🎉

Seja muito bem-vindo(a) à **{{ config('app.name') }}**! 🚀  

Seu cadastro foi realizado com sucesso, e agora você tem acesso a uma plataforma que vai **{{ $beneficio }}**.

## Como começar?
1. **Acesse sua conta via API:**  
```bash
curl --location '$url_login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "{Seu email}",
    "password": "{Sua Senha}"
}'
```  
2. **Explore nossas funcionalidades** e descubra tudo o que podemos fazer por você.  
3. **Precisa de ajuda?** Nossa equipe está sempre pronta para te apoiar!

Se precisar de algo, estamos à disposição!

Atenciosamente,  
**Equipe {{ config('app.name') }}**

@endcomponent
