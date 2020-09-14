/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const toolsRoutes = {
  path: '/tools',
  component: Layout,
  redirect: '/tools/spleeter',
  name: 'Tools',
  meta: {
    title: 'Tools',
    icon: 'skill',
    permissions: [''],
  },
  children: [
    {
      path: 'spleeter',
      component: () => import('@/views/tools/SeparateAudio.vue'),
      name: 'Separate Audio',
      meta: { title: 'Separate Audio', icon: 'skill', noCache: true },
    },
  ],
};

export default toolsRoutes;
