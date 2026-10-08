(function(){
  const toast=document.getElementById('toast'); let toastTimer;
  window.smartToast=function(message){if(!toast)return;clearTimeout(toastTimer);toast.textContent=message;toast.classList.add('show');toastTimer=setTimeout(()=>toast.classList.remove('show'),1800)};

  document.querySelectorAll('[data-toggle-password]').forEach(btn=>btn.addEventListener('click',()=>{
    const selector=btn.dataset.togglePassword; const input=document.querySelector(selector);
    if(!input)return; const showing=input.type==='password'; input.type=showing?'text':'password';
    const icon=btn.querySelector('i'); if(icon)icon.className=showing?'fa-regular fa-eye-slash':'fa-regular fa-eye';
  }));
  document.querySelectorAll('[data-password-toggle]').forEach(btn=>btn.addEventListener('click',()=>{
    const input=document.querySelector(btn.getAttribute('data-password-toggle')); if(!input)return;
    const showing=input.type==='password'; input.type=showing?'text':'password';
    const icon=btn.querySelector('i'); if(icon)icon.className=showing?'fa-regular fa-eye-slash':'fa-regular fa-eye';
  }));

  const modal=document.getElementById('actionConfirmModal');
  const title=document.getElementById('actionConfirmTitle');
  const message=document.getElementById('actionConfirmMessage');
  const cancel=document.getElementById('actionConfirmCancel');
  const confirm=document.getElementById('actionConfirmConfirm');
  let pending=null;
  function openConfirm(opts){ if(!modal)return; pending=opts.onConfirm||null; if(title)title.textContent=opts.title||'Confirm action'; if(message)message.textContent=opts.message||'Are you sure?'; if(confirm)confirm.innerHTML='<i class="fa-solid fa-check"></i> '+(opts.confirmText||'Confirm'); modal.classList.add('show'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
  function closeConfirm(){ if(!modal)return; modal.classList.remove('show'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow=''; pending=null; }
  cancel?.addEventListener('click',closeConfirm); modal?.addEventListener('click',e=>{if(e.target===modal)closeConfirm()}); confirm?.addEventListener('click',()=>{const fn=pending;closeConfirm();if(fn)fn()});
  document.addEventListener('click',e=>{
    const btn=e.target.closest('.delete-btn,[data-confirm]'); if(!btn)return;
    const form=btn.closest('form'); if(!form)return;
    e.preventDefault();
    openConfirm({title:btn.dataset.confirmTitle||'Delete this item?',message:btn.dataset.confirm||'This action cannot be undone.',confirmText:btn.dataset.confirmText||'Delete',onConfirm:()=>form.submit()});
  });
  document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeConfirm();document.getElementById('logoutModal')?.classList.remove('show')}});
})();
