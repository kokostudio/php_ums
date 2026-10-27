<?php

namespace App\Core;

class BrandIcon
{
    public const DEFAULT_ICON = 'cube';
    public const DEFAULT_COLOR = '#3b82f6';
    public const DEFAULT_SIZE = 32;

    public const ICONS = array (
  'cube' => 
  array (
    'name_en' => 'Cube',
    'name_th' => 'ลูกบาศก์แกนหลัก',
    'class' => 'ph-fill ph-cube',
    'paths' => 
    array (
      0 => 'M223.68,66.15,135.68,18a15.88,15.88,0,0,0-15.36,0l-88,48.17a16,16,0,0,0-8.32,14v95.64a16,16,0,0,0,8.32,14l88,48.17a15.88,15.88,0,0,0,15.36,0l88-48.17a16,16,0,0,0,8.32-14V80.18A16,16,0,0,0,223.68,66.15ZM128,120,47.65,76,128,32l80.35,44Zm8,99.64V133.83l80-43.78v85.76Z',
    ),
  ),
  'shield-check' => 
  array (
    'name_en' => 'Security Shield',
    'name_th' => 'โล่ความปลอดภัย',
    'class' => 'ph-fill ph-shield-check',
    'paths' => 
    array (
      0 => 'M208,40H48A16,16,0,0,0,32,56v56c0,52.72,25.52,84.67,46.93,102.19,23.06,18.86,46,25.26,47,25.53a8,8,0,0,0,4.2,0c1-.27,23.91-6.67,47-25.53C198.48,196.67,224,164.72,224,112V56A16,16,0,0,0,208,40Zm-34.32,69.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z',
    ),
  ),
  'sparkle' => 
  array (
    'name_en' => 'Sparkle',
    'name_th' => 'ประกายดาว AI',
    'class' => 'ph-fill ph-sparkle',
    'paths' => 
    array (
      0 => 'M208,144a15.78,15.78,0,0,1-10.42,14.94L146,178l-19,51.62a15.92,15.92,0,0,1-29.88,0L78,178l-51.62-19a15.92,15.92,0,0,1,0-29.88L78,110l19-51.62a15.92,15.92,0,0,1,29.88,0L146,110l51.62,19A15.78,15.78,0,0,1,208,144ZM152,48h16V64a8,8,0,0,0,16,0V48h16a8,8,0,0,0,0-16H184V16a8,8,0,0,0-16,0V32H152a8,8,0,0,0,0,16Zm88,32h-8V72a8,8,0,0,0-16,0v8h-8a8,8,0,0,0,0,16h8v8a8,8,0,0,0,16,0V96h8a8,8,0,0,0,0-16Z',
    ),
  ),
  'buildings' => 
  array (
    'name_en' => 'Enterprise',
    'name_th' => 'องค์กรธุรกิจ',
    'class' => 'ph-fill ph-buildings',
    'paths' => 
    array (
      0 => 'M239.73,208H224V96a16,16,0,0,0-16-16H164a4,4,0,0,0-4,4V208H144V32.41a16.43,16.43,0,0,0-6.16-13,16,16,0,0,0-18.72-.69L39.12,72A16,16,0,0,0,32,85.34V208H16.27A8.18,8.18,0,0,0,8,215.47,8,8,0,0,0,16,224H240a8,8,0,0,0,8-8.53A8.18,8.18,0,0,0,239.73,208ZM76,184a8,8,0,0,1-8.53,8A8.18,8.18,0,0,1,60,183.72V168.27A8.19,8.19,0,0,1,67.47,160,8,8,0,0,1,76,168Zm0-56a8,8,0,0,1-8.53,8A8.19,8.19,0,0,1,60,127.72V112.27A8.19,8.19,0,0,1,67.47,104,8,8,0,0,1,76,112Zm40,56a8,8,0,0,1-8.53,8,8.18,8.18,0,0,1-7.47-8.26V168.27a8.19,8.19,0,0,1,7.47-8.26,8,8,0,0,1,8.53,8Zm0-56a8,8,0,0,1-8.53,8,8.19,8.19,0,0,1-7.47-8.26V112.27a8.19,8.19,0,0,1,7.47-8.26,8,8,0,0,1,8.53,8Z',
    ),
  ),
  'chart-polar' => 
  array (
    'name_en' => 'Analytics',
    'name_th' => 'สถิติและข้อมูล',
    'class' => 'ph-fill ph-chart-polar',
    'paths' => 
    array (
      0 => 'M120,199.54v27.74a4,4,0,0,1-4.46,4,104.22,104.22,0,0,1-90.8-90.8,4,4,0,0,1,4-4.46H56.46A72.11,72.11,0,0,0,120,199.54ZM183.42,136H136v47.42A56.11,56.11,0,0,0,183.42,136ZM136,72.58V120h47.42A56.11,56.11,0,0,0,136,72.58ZM227.28,136H199.54A72.11,72.11,0,0,1,136,199.54v27.74a4,4,0,0,0,4.46,4,104.22,104.22,0,0,0,90.8-90.8A4,4,0,0,0,227.28,136Zm-27.74-16h27.74a4,4,0,0,0,4-4.46,104.22,104.22,0,0,0-90.8-90.8,4,4,0,0,0-4.46,4V56.46A72.11,72.11,0,0,1,199.54,120Zm-84-95.26a104.22,104.22,0,0,0-90.8,90.8,4,4,0,0,0,4,4.46H56.46A72.11,72.11,0,0,1,120,56.46V28.72A4,4,0,0,0,115.54,24.74ZM72.58,120H120V72.58A56.11,56.11,0,0,0,72.58,120ZM120,183.42V136H72.58A56.11,56.11,0,0,0,120,183.42Z',
    ),
  ),
  'compass' => 
  array (
    'name_en' => 'Compass',
    'name_th' => 'เข็มทิศนำทาง',
    'class' => 'ph-fill ph-compass',
    'paths' => 
    array (
      0 => 'M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm51.58,57.79-32,64a4.08,4.08,0,0,1-1.79,1.79l-64,32a4,4,0,0,1-5.37-5.37l32-64a4.08,4.08,0,0,1,1.79-1.79l64-32A4,4,0,0,1,179.58,81.79Z',
    ),
  ),
  'feather' => 
  array (
    'name_en' => 'Feather',
    'name_th' => 'ขนนกคล่องตัว',
    'class' => 'ph-fill ph-feather',
    'paths' => 
    array (
      0 => 'M211.84,134.81l-59.79,60.47,0,0a15.75,15.75,0,0,1-11.2,4.68H75.32L45.66,229.66a8,8,0,0,1-11.32-11.32l22.59-22.58h0L124.7,128H209A4,4,0,0,1,211.84,134.81ZM216.7,30.57a64,64,0,0,0-85.9,4.14l-9.6,9.48A4,4,0,0,0,120,47v63l55-55a8,8,0,0,1,11.31,11.31L140.71,112h88.38a4,4,0,0,0,3.56-2.16A64.08,64.08,0,0,0,216.7,30.57ZM62.83,167.23,104,126.06V70.76a4,4,0,0,0-6.81-2.84L60.69,104A15.9,15.9,0,0,0,56,115.31V164.4A4,4,0,0,0,62.83,167.23Z',
    ),
  ),
  'planet' => 
  array (
    'name_en' => 'Planet',
    'name_th' => 'ดาวเคราะห์สากล',
    'class' => 'ph-fill ph-planet',
    'paths' => 
    array (
      0 => 'M245.11,60.68c-7.65-13.19-27.85-16.16-58.5-8.66A96,96,0,0,0,32.81,140.3C5.09,169,5.49,186,10.9,195.32,16,204.16,26.64,208,40.64,208a124.11,124.11,0,0,0,28.79-4,96,96,0,0,0,153.78-88.25c12.51-13,20.83-25.35,23.66-35.92C248.83,72.51,248.24,66.07,245.11,60.68Zm-13.69,15c-6.11,22.78-48.65,57.31-87.52,79.64-67.81,39-113.62,41.52-119.16,32-1.46-2.51-.65-7.24,2.22-13a80.06,80.06,0,0,1,10.28-15.05,95.53,95.53,0,0,0,6.23,14.18,4,4,0,0,0,4,2.12,122.14,122.14,0,0,0,16.95-3.32c21.23-5.55,46.63-16.48,71.52-30.78s47-30.66,62.45-46.15A122.74,122.74,0,0,0,209.7,82.45a4,4,0,0,0,.17-4.52,96.26,96.26,0,0,0-9.1-12.46c14.21-2.35,27.37-2.17,30.5,3.24C232.19,70.28,232.24,72.63,231.42,75.69Z',
    ),
  ),
  'flame' => 
  array (
    'name_en' => 'Flame',
    'name_th' => 'เปลวไฟพลังงาน',
    'class' => 'ph-fill ph-flame',
    'paths' => 
    array (
      0 => 'M173.79,51.48a221.25,221.25,0,0,0-41.67-34.34,8,8,0,0,0-8.24,0A221.25,221.25,0,0,0,82.21,51.48C54.59,80.48,40,112.47,40,144a88,88,0,0,0,176,0C216,112.47,201.41,80.48,173.79,51.48ZM96,184c0-27.67,22.53-47.28,32-54.3,9.48,7,32,26.63,32,54.3a32,32,0,0,1-64,0Z',
    ),
  ),
  'lightning' => 
  array (
    'name_en' => 'Lightning',
    'name_th' => 'สายฟ้าความเร็วสูง',
    'class' => 'ph-fill ph-lightning',
    'paths' => 
    array (
      0 => 'M213.85,125.46l-112,120a8,8,0,0,1-13.69-7l14.66-73.33L45.19,143.49a8,8,0,0,1-3-13l112-120a8,8,0,0,1,13.69,7L153.18,90.9l57.63,21.61a8,8,0,0,1,3,12.95Z',
    ),
  ),
  'briefcase' => 
  array (
    'name_en' => 'Briefcase',
    'name_th' => 'กระเป๋าธุรกิจ',
    'class' => 'ph-fill ph-briefcase',
    'paths' => 
    array (
      0 => 'M152,112a8,8,0,0,1-8,8H112a8,8,0,0,1,0-16h32A8,8,0,0,1,152,112Zm80-40V200a16,16,0,0,1-16,16H40a16,16,0,0,1-16-16V72A16,16,0,0,1,40,56H80V48a24,24,0,0,1,24-24h48a24,24,0,0,1,24,24v8h40A16,16,0,0,1,232,72ZM96,56h64V48a8,8,0,0,0-8-8H104a8,8,0,0,0-8,8Zm120,57.61V72H40v41.61A184,184,0,0,0,128,136,184,184,0,0,0,216,113.61Z',
    ),
  ),
  'graduation-cap' => 
  array (
    'name_en' => 'Graduation Cap',
    'name_th' => 'การเรียนรู้ศึกษา',
    'class' => 'ph-fill ph-graduation-cap',
    'paths' => 
    array (
      0 => 'M176,207.24a119,119,0,0,0,16-7.73V240a8,8,0,0,1-16,0Zm11.76-88.43-56-29.87a8,8,0,0,0-7.52,14.12L171,128l17-9.06Zm64-29.87-120-64a8,8,0,0,0-7.52,0l-120,64a8,8,0,0,0,0,14.12L32,117.87v48.42a15.91,15.91,0,0,0,4.06,10.65C49.16,191.53,78.51,216,128,216a130,130,0,0,0,48-8.76V130.67L171,128l-43,22.93L43.83,106l0,0L25,96,128,41.07,231,96l-18.78,10-.06,0L188,118.94a8,8,0,0,1,4,6.93v73.64a115.63,115.63,0,0,0,27.94-22.57A15.91,15.91,0,0,0,224,166.29V117.87l27.76-14.81a8,8,0,0,0,0-14.12Z',
    ),
  ),
  'heart' => 
  array (
    'name_en' => 'Heart',
    'name_th' => 'หัวใจความใส่ใจ',
    'class' => 'ph-fill ph-heart',
    'paths' => 
    array (
      0 => 'M240,102c0,70-103.79,126.66-108.21,129a8,8,0,0,1-7.58,0C119.79,228.66,16,172,16,102A62.07,62.07,0,0,1,78,40c20.65,0,38.73,8.88,50,23.89C139.27,48.88,157.35,40,178,40A62.07,62.07,0,0,1,240,102Z',
    ),
  ),
  'globe' => 
  array (
    'name_en' => 'Globe',
    'name_th' => 'เครือข่ายระดับโลก',
    'class' => 'ph-fill ph-globe',
    'paths' => 
    array (
      0 => 'M128,24h0A104,104,0,1,0,232,128,104.12,104.12,0,0,0,128,24Zm78.36,64H170.71a135.28,135.28,0,0,0-22.3-45.6A88.29,88.29,0,0,1,206.37,88ZM216,128a87.61,87.61,0,0,1-3.33,24H174.16a157.44,157.44,0,0,0,0-48h38.51A87.61,87.61,0,0,1,216,128ZM128,43a115.27,115.27,0,0,1,26,45H102A115.11,115.11,0,0,1,128,43ZM102,168H154a115.11,115.11,0,0,1-26,45A115.27,115.27,0,0,1,102,168Zm-3.9-16a140.84,140.84,0,0,1,0-48h59.88a140.84,140.84,0,0,1,0,48Zm50.35,61.6a135.28,135.28,0,0,0,22.3-45.6h35.66A88.29,88.29,0,0,1,148.41,213.6Z',
    ),
  ),
  'crown' => 
  array (
    'name_en' => 'Crown',
    'name_th' => 'มงกุฎพรีเมียม',
    'class' => 'ph-fill ph-crown',
    'paths' => 
    array (
      0 => 'M248,80a28,28,0,1,0-51.12,15.77l-26.79,33L146,73.4a28,28,0,1,0-36.06,0L85.91,128.74l-26.79-33a28,28,0,1,0-26.6,12L47,194.63A16,16,0,0,0,62.78,208H193.22A16,16,0,0,0,209,194.63l14.47-86.85A28,28,0,0,0,248,80ZM128,40a12,12,0,1,1-12,12A12,12,0,0,1,128,40ZM24,80A12,12,0,1,1,36,92,12,12,0,0,1,24,80ZM220,92a12,12,0,1,1,12-12A12,12,0,0,1,220,92Z',
    ),
  ),
  'atom' => 
  array (
    'name_en' => 'Atom',
    'name_th' => 'อะตอมนวัตกรรม',
    'class' => 'ph-fill ph-atom',
    'paths' => 
    array (
      0 => 'M196.12,128c24.65-34.61,37.22-70.38,19.74-87.86S162.61,35.23,128,59.88C93.39,35.23,57.62,22.66,40.14,40.14S35.23,93.39,59.88,128c-24.65,34.61-37.22,70.38-19.74,87.86h0c5.63,5.63,13.15,8.14,21.91,8.14,18.47,0,42.48-11.17,66-27.88C151.47,212.83,175.47,224,194,224c8.76,0,16.29-2.52,21.91-8.14h0C233.34,198.38,220.77,162.61,196.12,128Zm8.43-76.55c7.64,7.64,2.48,32.4-18.52,63.28a300.33,300.33,0,0,0-21.19-23.57A302.47,302.47,0,0,0,141.27,70C172.15,49,196.91,43.81,204.55,51.45Zm-153.1,0c2.2-2.21,5.83-3.35,10.62-3.35C73.89,48.1,92.76,55,114.72,70A304,304,0,0,0,91.16,91.16,300.33,300.33,0,0,0,70,114.73C49,83.85,43.81,59.09,51.45,51.45Zm0,153.1C43.81,196.91,49,172.15,70,141.27a300.33,300.33,0,0,0,21.19,23.57A304.18,304.18,0,0,0,114.73,186C83.85,207,59.09,212.19,51.45,204.55ZM128,140a12,12,0,1,1,12-12A12,12,0,0,1,128,140Zm76.55,64.56c-7.64,7.65-32.4,2.48-63.28-18.52a304.18,304.18,0,0,0,23.57-21.19A300.33,300.33,0,0,0,186,141.27C207,172.15,212.19,196.91,204.55,204.55Z',
    ),
  ),
  'diamond' => 
  array (
    'name_en' => 'Diamond',
    'name_th' => 'เพชรคุณค่าสูง',
    'class' => 'ph-fill ph-diamond',
    'paths' => 
    array (
      0 => 'M240,128a15.85,15.85,0,0,1-4.67,11.28l-96.05,96.06a16,16,0,0,1-22.56,0h0l-96-96.06a16,16,0,0,1,0-22.56l96.05-96.06a16,16,0,0,1,22.56,0l96.05,96.06A15.85,15.85,0,0,1,240,128Z',
    ),
  ),
  'rocket' => 
  array (
    'name_en' => 'Rocket',
    'name_th' => 'จรวดก้าวกระโดด',
    'class' => 'ph-fill ph-rocket',
    'paths' => 
    array (
      0 => 'M152,224a8,8,0,0,1-8,8H112a8,8,0,0,1,0-16h32A8,8,0,0,1,152,224Zm71.62-68.17-12.36,55.63a16,16,0,0,1-25.51,9.11L158.51,200h-61L70.25,220.57a16,16,0,0,1-25.51-9.11L32.38,155.83a16.09,16.09,0,0,1,3.32-13.71l28.56-34.26a123.07,123.07,0,0,1,8.57-36.67c12.9-32.34,36-52.63,45.37-59.85a16,16,0,0,1,19.6,0c9.34,7.22,32.47,27.51,45.37,59.85a123.07,123.07,0,0,1,8.57,36.67l28.56,34.26A16.09,16.09,0,0,1,223.62,155.83Zm-139.23,34Q68.28,160.5,64.83,132.16L48,152.36,60.36,208l.18-.13ZM140,100a12,12,0,1,0-12,12A12,12,0,0,0,140,100Zm68,52.36-16.83-20.2q-3.42,28.28-19.56,57.69l23.85,18,.18.13Z',
    ),
  ),
  'stack' => 
  array (
    'name_en' => 'Stack',
    'name_th' => 'สถาปัตยกรรมชั้นข้อมูล',
    'class' => 'ph-fill ph-stack',
    'paths' => 
    array (
      0 => 'M220,169.09l-92,53.65L36,169.09A8,8,0,0,0,28,182.91l96,56a8,8,0,0,0,8.06,0l96-56A8,8,0,1,0,220,169.09Z',
      1 => 'M220,121.09l-92,53.65L36,121.09A8,8,0,0,0,28,134.91l96,56a8,8,0,0,0,8.06,0l96-56A8,8,0,1,0,220,121.09Z',
      2 => 'M28,86.91l96,56a8,8,0,0,0,8.06,0l96-56a8,8,0,0,0,0-13.82l-96-56a8,8,0,0,0-8.06,0l-96,56a8,8,0,0,0,0,13.82Z',
    ),
  ),
  'fingerprint' => 
  array (
    'name_en' => 'Fingerprint',
    'name_th' => 'การยืนยันตัวตน',
    'class' => 'ph-fill ph-fingerprint',
    'paths' => 
    array (
      0 => 'M126.42,24C70.73,24.85,25.21,70.09,24,125.81a103.53,103.53,0,0,0,13.52,53.54,4,4,0,0,0,7.1-.3,119.35,119.35,0,0,0,11.37-51A71.77,71.77,0,0,1,83,71.83a8,8,0,1,1,9.86,12.61A55.82,55.82,0,0,0,72,128.07a135.28,135.28,0,0,1-18.45,68.35,4,4,0,0,0,.61,4.85c2,2,4.09,4,6.25,5.82a4,4,0,0,0,6-1A151.18,151.18,0,0,0,85,158.49a8,8,0,1,1,15.68,3.19,167.33,167.33,0,0,1-21.07,53.64,4,4,0,0,0,1.6,5.63c2.47,1.25,5,2.41,7.57,3.47a4,4,0,0,0,5-1.61A183,183,0,0,0,120,128.28a8.16,8.16,0,0,1,7.44-8.21,8,8,0,0,1,8.56,8,198.94,198.94,0,0,1-25.21,97.16,4,4,0,0,0,2.95,5.92q4.55.63,9.21.86a4,4,0,0,0,3.67-2.1A214.88,214.88,0,0,0,152,128.8c.05-13.25-10.3-24.49-23.54-24.74A24,24,0,0,0,104,128a8.1,8.1,0,0,1-7.29,8,8,8,0,0,1-8.71-8,40,40,0,0,1,40.42-40c22,.23,39.68,19.17,39.57,41.16a231.37,231.37,0,0,1-20.52,94.57,4,4,0,0,0,4.62,5.51,103.49,103.49,0,0,0,10.26-3,4,4,0,0,0,2.35-2.22,243.76,243.76,0,0,0,11.48-34,8,8,0,1,1,15.5,4q-1.12,4.37-2.4,8.7a4,4,0,0,0,6.46,4.17A104,104,0,0,0,126.42,24ZM198,161.08a8,8,0,0,1-7.92,7,8.39,8.39,0,0,1-1-.06,8,8,0,0,1-6.95-8.93,252.57,252.57,0,0,0,1.92-31,56.08,56.08,0,0,0-56-56,56.78,56.78,0,0,0-7,.43,8,8,0,0,1-2-15.89,72.1,72.1,0,0,1,81,71.49A266.93,266.93,0,0,1,198,161.08Z',
    ),
  ),
);

    public static function all(): array
    {
        return self::ICONS;
    }

    public static function get(string $key): array
    {
        return self::ICONS[$key] ?? self::ICONS[self::DEFAULT_ICON];
    }

    /**
     * Renders pure SVG for an icon with specified color and size
     */
    public static function renderSvg(string $key, string $color = self::DEFAULT_COLOR, int $size = self::DEFAULT_SIZE): string
    {
        $icon = self::get($key);
        $pathsHtml = '';
        foreach ($icon['paths'] as $p) {
            $pathsHtml .= '<path fill="' . htmlspecialchars($color) . '" d="' . htmlspecialchars($p) . '"/>';
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="' . $size . '" height="' . $size . '" class="shrink-0">' . $pathsHtml . '</svg>';
    }

    /**
     * Returns an SVG Data URI for <link rel="icon">
     */
    public static function getFaviconSvgDataUri(string $key, string $color = self::DEFAULT_COLOR, string $bgColor = 'transparent'): string
    {
        $icon = self::get($key);
        $bgElement = '';
        if ($bgColor && $bgColor !== 'transparent') {
            $bgElement = '<rect width="256" height="256" rx="56" fill="' . htmlspecialchars($bgColor) . '"/>';
        }

        $pathsHtml = '';
        foreach ($icon['paths'] as $p) {
            $pathsHtml .= '<path fill="' . htmlspecialchars($color) . '" d="' . htmlspecialchars($p) . '"/>';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">' . $bgElement . $pathsHtml . '</svg>';
        return 'data:image/svg+xml,' . rawurlencode($svg);
    }

    /**
     * Returns current Favicon URL (either image file or SVG Data URI)
     */
    public static function getFaviconUrl(): string
    {
        $type = Settings::get('favicon_type', 'icon');
        $imageFavicon = Settings::get('site_favicon');

        if ($type === 'image' && !empty($imageFavicon) && file_exists(__DIR__ . '/../../public' . $imageFavicon)) {
            return htmlspecialchars($imageFavicon);
        }

        $iconKey = Settings::get('favicon_icon', self::DEFAULT_ICON);
        $color   = Settings::get('favicon_icon_color', self::DEFAULT_COLOR);
        $bg      = Settings::get('favicon_bg_color', 'transparent');

        return self::getFaviconSvgDataUri($iconKey, $color, $bg);
    }

    /**
     * Renders Logo in Sidebar or Header
     */
    public static function renderLogoHtml(string $extraClass = ''): string
    {
        $type = Settings::get('logo_type', 'icon');
        $imageLogo = Settings::get('site_logo');

        if ($type === 'image' && !empty($imageLogo) && file_exists(__DIR__ . '/../../public' . $imageLogo)) {
            return '<img src="' . htmlspecialchars($imageLogo) . '" alt="Logo" class="h-8 w-auto mr-3 ' . htmlspecialchars($extraClass) . '">';
        }

        $iconKey = Settings::get('logo_icon', self::DEFAULT_ICON);
        $color   = Settings::get('logo_icon_color', self::DEFAULT_COLOR);
        $size    = (int) Settings::get('logo_icon_size', self::DEFAULT_SIZE);
        $size    = max(20, min(64, $size));
        $icon    = self::get($iconKey);

        $pathsHtml = '';
        foreach ($icon['paths'] as $p) {
            $pathsHtml .= '<path fill="' . htmlspecialchars($color) . '" d="' . htmlspecialchars($p) . '"/>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="' . $size . '" height="' . $size . '" class="mr-2.5 shrink-0 ' . htmlspecialchars($extraClass) . '" style="display:inline-block;vertical-align:middle;">' . $pathsHtml . '</svg>';
    }

    /**
     * Renders Logo in Auth / Login Header
     */
    public static function renderAuthLogoHtml(): string
    {
        $loginLogo = Settings::get('login_logo') ?: Settings::get('site_logo');
        $type = Settings::get('logo_type', 'icon');

        if ($type === 'image' && !empty($loginLogo) && file_exists(__DIR__ . '/../../public' . $loginLogo)) {
            $logoSizeClass = Settings::get('login_logo_size', 'h-12');
            return '<img src="' . htmlspecialchars($loginLogo) . '" alt="Logo" class="' . htmlspecialchars($logoSizeClass) . ' w-auto drop-shadow-md transition-all duration-300">';
        }

        $iconKey = Settings::get('logo_icon', self::DEFAULT_ICON);
        $color   = Settings::get('logo_icon_color', self::DEFAULT_COLOR);
        $size    = (int) Settings::get('logo_icon_size', self::DEFAULT_SIZE);
        $size    = max(24, min(72, $size + 8)); // slightly larger for auth card
        $icon    = self::get($iconKey);

        $pathsHtml = '';
        foreach ($icon['paths'] as $p) {
            $pathsHtml .= '<path fill="' . htmlspecialchars($color) . '" d="' . htmlspecialchars($p) . '"/>';
        }

        return '<div class="inline-flex items-center justify-center p-2 rounded-xl bg-white/10 dark:bg-white/5 backdrop-blur-sm border border-white/10 shadow-lg mr-3 shrink-0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="' . $size . '" height="' . $size . '" style="display:inline-block;vertical-align:middle;">' . $pathsHtml . '</svg></div>';
    }
}