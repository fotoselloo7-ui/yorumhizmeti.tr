/* Browser-generated notification tones: no third-party audio files. */
window.NvDeskAlerts=(()=>{
  let context=null,active=false;
  function unlock(){
    try{
      const Audio=window.AudioContext||window.webkitAudioContext;
      if(!Audio)return false;
      if(!context)context=new Audio();
      context.resume();
      active=true;return true;
    }catch(e){return false}
  }
  function play(preset='chime'){
    if(!active||!context||preset==='off'||document.hidden&&document.visibilityState!=='visible')return;
    const frequencies=preset==='soft'?[620]:preset==='digital'?[780,1050,780]:[587,784];
    frequencies.forEach((freq,i)=>{
      const start=context.currentTime+i*.15;
      const oscillator=context.createOscillator(),volume=context.createGain();
      oscillator.type=preset==='digital'?'triangle':'sine';
      oscillator.frequency.value=freq;
      volume.gain.setValueAtTime(.001,start);
      volume.gain.exponentialRampToValueAtTime(.11,start+.025);
      volume.gain.exponentialRampToValueAtTime(.001,start+.21);
      oscillator.connect(volume);volume.connect(context.destination);
      oscillator.start(start);oscillator.stop(start+.22);
    });
  }
  function notice(title,text){
    if(typeof Notification!=='undefined'&&Notification.permission==='granted'){
      try{new Notification(title,{body:text,tag:'nv-desk-alert'})}catch(e){}
    }
  }
  async function enable(){
    unlock();
    if(typeof Notification!=='undefined'&&Notification.permission==='default'){
      try{await Notification.requestPermission()}catch(e){}
    }
  }
  return{unlock,enable,play,notice};
})();